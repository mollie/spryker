import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

declare global {
  interface Window {
    Mollie: (profileId: string, options?: MollieOptions) => MollieInstance;
  }
}

const MOLLIE_CREDIT_CARD_PAYMENT_METHOD_IDENTIFIER = 'mollieCreditCardPayment';

interface MollieOptions {
  locale?: string;
  testmode?: boolean;
}

interface MollieInstance {
  createComponent(type: string, options?: any): any;
  createToken(): Promise<any>;
}
export default class MollieCreditCardComponent extends Component {
    protected scriptLoader: ScriptLoader;
    protected mollie: MollieInstance;
    protected cardNumber: Object;
    protected cardNumberError: HTMLElement;
    protected cardHolder: Object;
    protected cardHolderError: HTMLElement;
    protected expiryDate: Object;
    protected expiryDateError: HTMLElement;
    protected verificationCode: Object;
    protected verificationCodeError: HTMLElement;
    protected form: HTMLFormElement;
    protected cardToken: HTMLInputElement;
    protected errorOutput: HTMLDivElement;

    protected readyCallback(): void {}

    protected init(): void {
        this.cardToken = <HTMLInputElement>document.querySelector(this.cardTokenClass);
        this.scriptLoader = <ScriptLoader>this.querySelector(this.scriptLoaderTag);
        this.errorOutput = <HTMLDivElement>this.querySelector(this.errorTokenOutputClass);
        this.form = <HTMLFormElement>document.querySelector(this.formSelector);

        this.mapEvents();
    }

    protected mapEvents(): void
    {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());
        this.form.addEventListener('submit', (event: Event) => this.onSubmit(event));
    }

    protected onScriptLoad(): void
    {
        this.initMollieInstance();
        this.initComponents();
        this.initErrorElements();
        this.mapComponentEvents();
    }

    protected initMollieInstance(): void
    {
        this.mollie = window.Mollie(this.profileId,  { locale: this.locale, testmode: this.testMode });
    }

    protected initComponents(): void
    {
        this.cardHolder = this.mollie.createComponent(`cardHolder`);
        this.cardHolder.mount('#card-holder')

        this.cardNumber = this.mollie.createComponent(`cardNumber`);
        this.cardNumber.mount('#card-number');

        this.expiryDate = this.mollie.createComponent(`expiryDate`);
        this.expiryDate.mount('#expiry-date');

        this.verificationCode = this.mollie.createComponent(`verificationCode`);
        this.verificationCode.mount('#verification-code');

    }

    protected initErrorElements(): void
    {
        this.cardHolderError = document.querySelector('#card-holder-error');
        this.cardNumberError = document.querySelector('#card-number-error');
        this.expiryDateError = document.querySelector('#expiry-date-error');
        this.verificationCodeError = document.querySelector('#verification-code-error');
    }

    protected mapComponentEvents(): void
    {
        this.cardHolder.addEventListener('change', (event: Event) => this.onChangeCardHolder(event));
        this.cardNumber.addEventListener('change', (event: Event) => this.onChangeCardNumber(event));
        this.expiryDate.addEventListener('change', (event: Event) => this.onChangeExpiryDate(event));
        this.verificationCode.addEventListener('change', (event: Event) => this.onChangeVerificationCode(event));
    }

    protected onChangeCardHolder(event: Event): void
    {
        if (event.error && event.touched) {
            this.cardHolderError.textContent = event.error;
        } else {
            this.cardHolderError.textContent = '';
        }
    }

    protected onChangeCardNumber(event: Event): void
    {
        if (event.error && event.touched) {
            this.cardNumberError.textContent = event.error;
        } else {
            this.cardNumberError.textContent = '';
        }
    }

    protected onChangeExpiryDate(event: Event): void
    {
        if (event.error && event.touched) {
            this.expiryDateError.textContent = event.error;
        } else {
            this.expiryDateError.textContent = '';
        }
    }

    protected onChangeVerificationCode(event: Event): void
    {
        if (event.error && event.touched) {
            this.verificationCodeError.textContent = event.error;
        } else {
            this.verificationCodeError.textContent = '';
        }
    }

    protected onSubmit(event: Event): void
    {
        if (!this.isCurrentPaymentMethod) {
            return;
        }

        event.preventDefault();
        this.mollie.createToken().then((result) => {
             const { token, error } = result;

             if (error) {
                 this.errorOutput.textContent = error.message;
                 return;
             }

             this.cardToken.setAttribute('value', token);
             this.form.submit();
        });
    }

    protected get isCurrentPaymentMethod(): boolean | null {
        const currentPaymentMethodInput = <HTMLInputElement>document.querySelector(this.selectedPaymentMethod);

        return currentPaymentMethodInput?.value
            ? currentPaymentMethodInput.value === MOLLIE_CREDIT_CARD_PAYMENT_METHOD_IDENTIFIER
            : null;
    }

    protected get cardTokenClass(): string {
        return '.card-token';
    }

    protected get errorTokenOutputClass(): string
    {
        return '.error-token-output';
    }

    protected get scriptLoaderTag(): string
    {
        return 'script-loader';
    }

    protected get formSelector(): string {
        return this.getAttribute('form-selector');
    }

    protected get locale(): string {
        return this.getAttribute('locale');
    }

    protected get selectedPaymentMethod(): string
    {
        return this.getAttribute('selected-payment-method')
    }

    protected get profileId(): string
    {
        return this.getAttribute('profile-id');
    }

    protected get testMode(): boolean
    {
        return this.getAttribute('test-mode') === 'true';
    }
}
