import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

declare global {
  interface Window {
    Mollie2: {
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
  create(type: string, options?: MollieExpressComponentOptions): MollieComponentInstance;
  on(eventName: string, handler: (event: MollieSubmitEvent) => void): void;
}

interface MollieExpressComponentButtonOptions {
  visibility: 'hidden';
}

interface MollieExpressComponentOptions {
  buttons: {
    [expressMethod: string]: MollieExpressComponentButtonOptions;
  };
}

interface MollieResolveEnabledMethodsResponse {
  expressMethods: {
    [expressMethod: string]: boolean;
  };
}

interface MollieCreateSessionResponse {
  clientAccessToken: string;
}

interface MolliePlaceOrderResponse {
  isSuccessful: boolean;
  orderReference: string | null;
  errors: string[];
}

export default class MollieExpressCheckoutComponent extends Component {
    protected scriptLoader: ScriptLoader;
    protected checkout: MollieCheckoutInstance;
    protected expressMethods: { [expressMethod: string]: boolean };

    protected readyCallback(): void {}

    protected init(): void {
        this.scriptLoader = <ScriptLoader>this.querySelector(this.scriptLoaderTag);
        this.mapEvents();
        this.mapDisabledOverlayEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());
    }

    protected onScriptLoad(): void {
        this.resolveEnabledMethods();
    }

    protected resolveEnabledMethods(): void {
        fetch(this.expressCheckoutResolveEnabledMethodsEndpoint, { method: 'POST', credentials: 'same-origin' })
            .then((response) => response.json())
            .then((resolveEnabledMethodsResponse: MollieResolveEnabledMethodsResponse) => this.onEnabledMethodsResolved(resolveEnabledMethodsResponse))
            .catch(() => {});
    }

    protected onEnabledMethodsResolved(resolveEnabledMethodsResponse: MollieResolveEnabledMethodsResponse): void {
        const hasEnabledExpressMethod = Object.values(resolveEnabledMethodsResponse.expressMethods).includes(true);

        if (!hasEnabledExpressMethod) {
            return;
        }

        this.expressMethods = resolveEnabledMethodsResponse.expressMethods;
        this.createSession();
    }

    protected createSession(): void {
        fetch(this.expressCheckoutCreateSessionEndpoint, { method: 'POST', credentials: 'same-origin' })
            .then((response) => response.json())
            .then((createSessionResponse: MollieCreateSessionResponse) => this.onSessionCreated(createSessionResponse))
            .catch(() => {});
    }

    protected onSessionCreated(createSessionResponse: MollieCreateSessionResponse): void {
        if (!createSessionResponse.clientAccessToken) {
            return;
        }

        this.checkout = window.Mollie2.Checkout(createSessionResponse.clientAccessToken, { locale: this.locale });
        this.mapSubmitEvent();
        this.mountExpressComponent();
    }

    protected mapSubmitEvent(): void {
        this.checkout.on('submit', (event: MollieSubmitEvent) => this.onSubmit(event));
    }

    protected mountExpressComponent(): void {
        const expressComponent = this.checkout.create('express-component', this.buildExpressComponentOptions());
        expressComponent.mount(this.mountSelector);
    }

    protected buildExpressComponentOptions(): MollieExpressComponentOptions {
        const buttons: { [expressMethod: string]: MollieExpressComponentButtonOptions } = {};

        Object.entries(this.expressMethods).forEach(([expressMethod, isEnabled]) => {
            if (!isEnabled) {
                buttons[expressMethod] = { visibility: 'hidden' };
            }
        });

        return { buttons };
    }

    protected mapDisabledOverlayEvents(): void {
        const overlay = this.querySelector(`.${this.name}__disabled-overlay`);

        if (!overlay) {
            return;
        }

        overlay.addEventListener('click', () => this.showDisabledMessage());
    }

    protected showDisabledMessage(): void {
        this.querySelector(`.${this.name}__disabled-message`)?.classList.remove('is-hidden');
    }

    protected onSubmit(event: MollieSubmitEvent): void {
        if (this.isDisabled) {
            event.reject();
            this.showDisabledMessage();

            return;
        }

        event.defer();
        this.hideErrorMessage();

        fetch(this.expressCheckoutPlaceOrderEndpoint, { method: 'POST', credentials: 'same-origin' })
            .then((response) => response.json())
            .then((placeOrderResponse: MolliePlaceOrderResponse) => {
                if (placeOrderResponse.isSuccessful) {
                    event.resolve();

                    return;
                }

                event.reject();
                this.showErrorMessage(placeOrderResponse.errors);
            })
            .catch(() => {
                event.reject();
                this.showErrorMessage([]);
            });
    }

    protected showErrorMessage(errors: string[]): void {
        const errorMessage = this.querySelector(`.${this.name}__error-message`);

        if (!errorMessage) {
            return;
        }

        errorMessage.textContent = errors.length ? errors.join(' ') : this.placeOrderErrorMessage;
        errorMessage.classList.remove('is-hidden');
    }

    protected hideErrorMessage(): void {
        this.querySelector(`.${this.name}__error-message`)?.classList.add('is-hidden');
    }

    protected get scriptLoaderTag(): string {
        return 'script-loader';
    }

    protected get isDisabled(): boolean {
        return this.getAttribute('is-disabled') === 'true';
    }

    protected get mountSelector(): string {
        return '#' + this.getAttribute('mount-id');
    }

    protected get locale(): string {
        return this.getAttribute('locale');
    }

    protected get expressCheckoutResolveEnabledMethodsEndpoint(): string {
        return this.getAttribute('express-checkout-resolve-enabled-methods-endpoint');
    }

    protected get expressCheckoutCreateSessionEndpoint(): string {
        return this.getAttribute('express-checkout-create-session-endpoint');
    }

    protected get expressCheckoutPlaceOrderEndpoint(): string {
        return this.getAttribute('express-checkout-place-order-endpoint');
    }

    protected get placeOrderErrorMessage(): string {
        return this.getAttribute('place-order-error-message');
    }
}
