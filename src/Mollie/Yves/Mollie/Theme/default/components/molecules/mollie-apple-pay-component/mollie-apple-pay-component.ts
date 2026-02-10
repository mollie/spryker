import Component from 'ShopUi/models/component';
import { EVENT_UPDATE_DYNAMIC_MESSAGES } from 'ShopUi/components/organisms/dynamic-notification-area/dynamic-notification-area';

export default class MollieApplePayComponent extends Component {
    protected applePayInput: HTMLInputElement | null = null;
    protected appleToggler: HTMLElement | null = null;

    protected init(): void {
        this.findApplePayInput();
        this.toggleApplePayAvailability();
        this.preventUnsupportedApplePayOnSubmit();
    }

    protected findApplePayInput(): void {
        this.appleToggler = document.querySelector(
            'toggler-radio#paymentForm_paymentSelection_mollieApplePayPayment'
        ) as HTMLElement | null;

        if (!this.appleToggler) {
            return;
        }

        const inputs = this.appleToggler.getElementsByTagName('input');

        if (!inputs || inputs.length === 0) {
            return;
        }

        this.applePayInput = inputs[0] as HTMLInputElement;
    }

    protected isApplePaySupported(): boolean {
        const platform = navigator.platform.toLowerCase();
        const userAgent = navigator.userAgent.toLowerCase();

        return platform.includes('mac') || /iphone|ipad|ipod/.test(userAgent);
    };

    protected toggleApplePayAvailability(): void {
        if (!this.applePayInput) return;

        if (!this.isApplePaySupported()) {
            this.applePayInput.checked = false;
            this.applePayInput.disabled = true;

            if (this.appleToggler) {
                this.appleToggler.classList.add('mollie-apple-pay-component__is-disabled');
            }
        }
    }

    protected preventUnsupportedApplePayOnSubmit(): void {
        const form = document.querySelector('form[name="paymentForm"]') as HTMLFormElement | null;

        if (!form) {
            return;
        }

        form.addEventListener('submit', (event) => {
            if (!this.applePayInput) return;

            if (!this.isApplePaySupported() && this.applePayInput.checked) {
                event.preventDefault();
                this.applePayInput.checked = false;
                this.showFlashMessage('Apple Pay is not supported on your device/browser.', 'alert');
            }
        });
    }

    protected showFlashMessage(message: string, type: 'alert' | 'success' | 'warning' = 'alert'): void {
        try {
            const htmlMessage =
                `<section class="flash-message-list" data-qa="component flash-message-list">
                    <flash-message class="custom-element flash-message flash-message--${type}" data-qa="component flash-message">
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
        } catch (err) {
            console.error(err);
        }
    }
}
