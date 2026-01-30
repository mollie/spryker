import Component from 'ShopUi/models/component';

export default class MollieApplePayComponent extends Component {
    protected applePayInput: HTMLInputElement | null = null;
    protected checkoutList: HTMLElement | null = null;
    protected heading: HTMLElement | null = null;

    protected init(): void {
        this.checkApplePay();
    }

    /**
     * Check if the current OS supports Apple Pay.
     * @protected
     */
    protected isApplePaySupportedOS(): boolean {
        const platform = navigator.platform.toLowerCase();
        const userAgent = navigator.userAgent.toLowerCase();

        return platform.includes('mac') || /iphone|ipad|ipod/.test(userAgent);
    };

    protected checkApplePay(): void {
         const applePaySupported = this.isApplePaySupportedOS();

        if (!applePaySupported) {
            console.warn('Apple Pay is not supported on this OS. Hiding Apple Pay option.');
            this.hideApplePay();
        }

        return;
    }

    protected hideApplePay(): void {
        this.applePayInput = document.getElementById(
            'paymentForm_paymentSelection_mollieApplePayPayment',
        ) as HTMLInputElement | null;

        if (!this.applePayInput) {
            return;
        }

        this.checkoutList = this.applePayInput.closest('ul.checkout-list');

        if (!this.checkoutList) {
            return;
        }

        this.heading = this.checkoutList.previousElementSibling as HTMLElement | null;

        this.applePayInput.checked = false;
        this.applePayInput.disabled = true;

        this.checkoutList.style.display = 'none';

        if (this.heading && this.heading.tagName === 'H5') {
            this.heading.style.display = 'none';
        }
    }
}
