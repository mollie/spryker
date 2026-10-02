import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';
import { EVENT_UPDATE_DYNAMIC_MESSAGES } from 'ShopUi/components/organisms/dynamic-notification-area/dynamic-notification-area';

const APPLE_PAY_VERSION = 3;
const APPLE_PAY_MERCHANT_CAPABILITIES = ['supports3DS'];
const APPLE_PAY_SUPPORTED_NETWORKS = ['amex', 'maestro', 'masterCard', 'visa', 'vPay'];

interface ApplePayPaymentRequest {
    countryCode: string;
    currencyCode: string;
    merchantCapabilities: string[];
    supportedNetworks: string[];
    total: {
        label: string;
        amount: string;
    };
}

interface ApplePayValidateMerchantEvent {
    validationURL: string;
}

interface ApplePayPaymentAuthorizedEvent {
    payment: {
        token: object;
    };
}

interface ApplePayPaymentAuthorizationResult {
    status: number;
}

interface ApplePaySessionInstance {
    onvalidatemerchant: (event: ApplePayValidateMerchantEvent) => void;
    onpaymentauthorized: (event: ApplePayPaymentAuthorizedEvent) => void;
    begin(): void;
    abort(): void;
    completeMerchantValidation(applePayPaymentSession: object): void;
    completePayment(result: ApplePayPaymentAuthorizationResult): void;
}

interface ApplePaySessionConstructor {
    new (version: number, paymentRequest: ApplePayPaymentRequest): ApplePaySessionInstance;
    canMakePayments(): boolean;
    STATUS_SUCCESS: number;
}

declare global {
    interface Window {
        ApplePaySession?: ApplePaySessionConstructor;
    }
}

export default class MollieApplePayDirectComponent extends Component {
    protected scriptLoader: ScriptLoader;
    protected form: HTMLFormElement;
    protected applePayDirectInput: HTMLInputElement | null;
    protected applePayPaymentTokenInput: HTMLInputElement;

    protected readyCallback(): void {}

    protected init(): void {
        this.scriptLoader = <ScriptLoader>this.querySelector(this.scriptLoaderTag);
        this.form = <HTMLFormElement>document.querySelector(this.formSelector);
        this.applePayDirectInput = <HTMLInputElement | null>document.querySelector(`input#${this.applePayDirectInputId}`);
        this.applePayPaymentTokenInput = <HTMLInputElement>document.querySelector(this.applePayPaymentTokenSelector);

        this.mapEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());
        this.form.addEventListener('submit', (event: Event) => this.onSubmit(event));
    }

    protected onScriptLoad(): void {
        if (this.isApplePayAvailable()) {
            return;
        }

        this.disableApplePayDirectInput();
    }

    protected onSubmit(event: Event): void {
        if (!this.isCurrentPaymentMethod) {
            return;
        }

        event.preventDefault();

        if (!this.isApplePayAvailable()) {
            this.showFlashMessage(this.unavailableMessage);

            return;
        }

        this.startApplePaySession();
    }

    protected isApplePayAvailable(): boolean {
        const applePaySession = window.ApplePaySession;

        if (!applePaySession) {
            return false;
        }

        return applePaySession.canMakePayments();
    }

    protected disableApplePayDirectInput(): void {
        if (!this.applePayDirectInput) {
            return;
        }

        this.applePayDirectInput.checked = false;
        this.applePayDirectInput.disabled = true;
    }

    protected startApplePaySession(): void {
        const applePaySessionConstructor = window.ApplePaySession;
        const paymentRequest = this.createPaymentRequest();
        const applePaySession = new applePaySessionConstructor(APPLE_PAY_VERSION, paymentRequest);

        applePaySession.onvalidatemerchant = (validateMerchantEvent: ApplePayValidateMerchantEvent) =>
            this.onValidateMerchant(applePaySession, validateMerchantEvent);
        applePaySession.onpaymentauthorized = (paymentAuthorizedEvent: ApplePayPaymentAuthorizedEvent) =>
            this.onPaymentAuthorized(applePaySessionConstructor, applePaySession, paymentAuthorizedEvent);

        applePaySession.begin();
    }

    protected createPaymentRequest(): ApplePayPaymentRequest {
        return {
            countryCode: this.countryCode,
            currencyCode: this.currencyCode,
            merchantCapabilities: APPLE_PAY_MERCHANT_CAPABILITIES,
            supportedNetworks: APPLE_PAY_SUPPORTED_NETWORKS,
            total: {
                label: this.totalLabel,
                amount: this.amount,
            },
        };
    }

    protected onValidateMerchant(
        applePaySession: ApplePaySessionInstance,
        validateMerchantEvent: ApplePayValidateMerchantEvent,
    ): void {
        this.requestApplePayPaymentSession(validateMerchantEvent.validationURL)
            .then((applePayPaymentSession: object) => {
                applePaySession.completeMerchantValidation(applePayPaymentSession);
            })
            .catch((error: Error) => {
                console.error(error);
                applePaySession.abort();
                this.showFlashMessage(this.unavailableMessage);
            });
    }

    protected requestApplePayPaymentSession(validationUrl: string): Promise<object> {
        const requestOptions: RequestInit = {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ validationUrl }),
        };

        return fetch(this.applePayPaymentSessionEndpoint, requestOptions).then((response: Response) => {
            if (!response.ok) {
                throw new Error(`Apple Pay payment session request failed with status ${response.status}`);
            }

            return response.json();
        });
    }

    protected onPaymentAuthorized(
        applePaySessionConstructor: ApplePaySessionConstructor,
        applePaySession: ApplePaySessionInstance,
        paymentAuthorizedEvent: ApplePayPaymentAuthorizedEvent,
    ): void {
        const applePayPaymentToken = JSON.stringify(paymentAuthorizedEvent.payment.token);
        this.applePayPaymentTokenInput.value = applePayPaymentToken;

        applePaySession.completePayment({ status: applePaySessionConstructor.STATUS_SUCCESS });
        this.form.submit();
    }

    protected showFlashMessage(message: string): void {
        const htmlMessage =
            `<section class="flash-message-list" data-qa="component flash-message-list">
                <flash-message class="custom-element flash-message flash-message--alert" data-qa="component flash-message">
                    <div class="flash-message__message container grid">
                        <div class="col flash-message__content">
                            <div class="flash-message__text">${message}</div>
                            <span class="flash-message__static-link">Ok!</span>
                        </div>
                    </div>
                </flash-message>
            </section>`;

        const dynamicNotificationCustomEvent = new CustomEvent(EVENT_UPDATE_DYNAMIC_MESSAGES, {
            detail: htmlMessage,
        });

        document.dispatchEvent(dynamicNotificationCustomEvent);
    }

    protected get isCurrentPaymentMethod(): boolean {
        if (!this.applePayDirectInput) {
            return false;
        }

        return this.applePayDirectInput.checked;
    }

    protected get scriptLoaderTag(): string {
        return 'script-loader';
    }

    protected get formSelector(): string {
        return this.getAttribute('form-selector');
    }

    protected get applePayDirectInputId(): string {
        return this.getAttribute('apple-pay-direct-input-id');
    }

    protected get applePayPaymentTokenSelector(): string {
        return this.getAttribute('apple-pay-payment-token-selector');
    }

    protected get applePayPaymentSessionEndpoint(): string {
        return this.getAttribute('apple-pay-payment-session-endpoint');
    }

    protected get amount(): string {
        return this.getAttribute('amount');
    }

    protected get currencyCode(): string {
        return this.getAttribute('currency-code');
    }

    protected get countryCode(): string {
        return this.getAttribute('country-code');
    }

    protected get totalLabel(): string {
        return this.getAttribute('total-label');
    }

    protected get unavailableMessage(): string {
        return this.getAttribute('unavailable-message');
    }
}
