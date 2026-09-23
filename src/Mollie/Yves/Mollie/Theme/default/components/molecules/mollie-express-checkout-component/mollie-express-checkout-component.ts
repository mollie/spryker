import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

declare global {
  interface Window {
    Mollie: {
      Checkout(clientAccessToken: string, options?: MollieCheckoutOptions): MollieCheckoutInstance;
    };
  }
}

interface MollieCheckoutOptions {
  locale?: string;
}

interface MollieSubmitEvent {
  defer(): void;
  resolve(): void;
  reject(): void;
}

interface MollieComponentInstance {
  mount(selector: string): void;
}

interface MollieCheckoutInstance {
  create(type: string, options?: object): MollieComponentInstance;
  on(eventName: string, handler: (event: MollieSubmitEvent) => void): void;
}

interface MollieExpressCheckoutInitResponse {
  enabledMethods: string[];
  clientAccessToken?: string;
}

export default class MollieExpressCheckoutComponent extends Component {
    protected scriptLoader: ScriptLoader;
    protected checkout: MollieCheckoutInstance;

    protected readyCallback(): void {}

    protected init(): void {
        this.scriptLoader = <ScriptLoader>this.querySelector(this.scriptLoaderTag);
        this.mapEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());
    }

    protected onScriptLoad(): void {
        this.initExpressCheckout();
    }

    protected initExpressCheckout(): void {
        fetch(this.expressCheckoutInitEndpoint, { method: 'POST', credentials: 'same-origin' })
            .then((response) => response.json())
            .then((initResponse: MollieExpressCheckoutInitResponse) => this.onInitResponse(initResponse))
            .catch(() => {});
    }

    protected onInitResponse(initResponse: MollieExpressCheckoutInitResponse): void {
        if (!initResponse.enabledMethods.length || !initResponse.clientAccessToken) {
            return;
        }

        this.checkout = window.Mollie.Checkout(initResponse.clientAccessToken, { locale: this.locale });
        this.mapSubmitEvent();
        this.mountExpressComponent();
    }

    protected mapSubmitEvent(): void {
        this.checkout.on('submit', (event: MollieSubmitEvent) => this.onSubmit(event));
    }

    protected mountExpressComponent(): void {
        const expressComponent = this.checkout.create('express-component');
        expressComponent.mount(this.mountSelector);
    }

    protected onSubmit(event: MollieSubmitEvent): void {
        event.defer();
        event.reject();
    }

    protected get scriptLoaderTag(): string {
        return 'script-loader';
    }

    protected get mountSelector(): string {
        return '#' + this.getAttribute('mount-id');
    }

    protected get locale(): string {
        return this.getAttribute('locale');
    }

    protected get expressCheckoutInitEndpoint(): string {
        return this.getAttribute('express-checkout-init-endpoint');
    }
}
