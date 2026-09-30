import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

declare global {
  interface Window {
    Mollie2: {
      Checkout(clientAccessToken: string, options?: MollieCheckoutOptions): MollieCheckoutInstance;
    };
    // TODO: POC only - set in the browser console to choose how onSubmit ends.
    mollieExpressPocDecision?: 'resolve' | 'reject';
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

    // TODO: POC only - proves that defer() pauses the payment until resolve()/reject() is called asynchronously.
    // Decision comes from window.mollieExpressPocDecision ('resolve' charges, anything else rejects = no charge).
    // Express checkout is disabled until the cart's address form is saved: the overlay over the Mollie buttons
    // catches the click and shows the message instead of starting the payment.
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
        // Safety net: the overlay should prevent this, but never let a payment through without addresses.
        if (this.isDisabled) {
            event.reject();
            this.showDisabledMessage();

            return;
        }

        const startedAt = Date.now();
        const log = (message: string) => console.log(`[mollie-express-poc] +${Date.now() - startedAt}ms ${message}`);
        const waitMs = 10000;

        log('onSubmit called by Mollie');
        event.defer();
        log(`defer() called - payment should now be paused for ${waitMs / 1000}s`);

        const countdown = window.setInterval(() => log('still waiting, Mollie has not created the payment yet'), 2000);

        window.setTimeout(() => {
            window.clearInterval(countdown);

            if (window.mollieExpressPocDecision === 'resolve') {
                log('calling resolve() - Mollie should create and charge the payment now');
                event.resolve();

                return;
            }

            log('calling reject() - Mollie should abort, no payment is created');
            event.reject();
        }, waitMs);

        log('onSubmit returned - JS thread is free, Mollie is waiting for our decision');
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
}
