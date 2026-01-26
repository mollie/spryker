import Component from 'ShopUi/models/component';

export default class MollieCreditCardComponent extends Component {
    protected applePayInput: HTMLInputElement | null = null;
    protected listItem: HTMLElement | null = null;

    protected readyCallback(): void {
        this.checkApplePay();
    }

    protected checkApplePay(): void {
        // Apple Pay capability check
        const applePaySupported: boolean =
            typeof window.ApplePaySession !== 'undefined' &&
            ApplePaySession.canMakePayments();

        if (applePaySupported) {
            console.log('Apple Pay is supported on this device');
            return;
        } else {
            console.log('Apple Pay is not supported on this device');
        }

        // Find Apple Pay radio input by id or value
        this.applePayInput =
            document.getElementById('paymentForm_paymentSelection_mollieApplePayPayment');

        if (!this.applePayInput) {
            return;
        }

        // Walk up to <li class="checkout-list__item">
        this.listItem = this.applePayInput.closest('li.checkout-list__item');

        if (!this.listItem) {
            return;
        }

        // Disable input to avoid accidental submission
        this.applePayInput.disabled = true;
        this.applePayInput.checked = false;

        // Hide entire payment method block
        this.listItem.style.display = 'none';
        console.log('Hidden Apple Pay option');
    }
}