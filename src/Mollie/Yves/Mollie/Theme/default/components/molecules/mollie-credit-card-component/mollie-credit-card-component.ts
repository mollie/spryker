import Component from 'ShopUi/models/component';

declare global {
  interface Window {
    Mollie: (profileId: string, options?: MollieOptions) => MollieInstance;
  }
}

interface MollieOptions {
  locale?: string;
  testmode?: boolean;
}

var options = {
  styles: {
    base: {
      fontSize: "18px",
      color: "#162941",
    },
  },
};

interface MollieInstance {
  createComponent(type: string, options?: any): any;
  createToken(): Promise<any>;
  // Add other Mollie methods you use
}
export default class MollieCreditCardComponent extends Component {

    protected readyCallback(): void {}

    protected init(): void {
        const mollie = window.Mollie('pfl_3RkSN1zuPE', { locale: 'de_DE', testmode: true });
        console.log('Testing if mollie works: ' + mollie);
        console.log(123);

        var cardHolder = mollie.createComponent("cardHolder", options);
        cardHolder.mount("#card-holder");


        cardHolder.addEventListener("change", function (event) {
          this.toggleFieldClass({ elementId: "card-holder", toggleClassesObject: event });

          if (event.error && event.touched) {
            cardHolderError.textContent = event.error;
          } else {
            cardHolderError.textContent = "";
          }
        });

        var cardNumber = mollie.createComponent("cardNumber", options);
        cardNumber.mount("#card-number");

        var cardNumberError = document.getElementById("card-number-error");

        cardNumber.addEventListener("change", function (event) {
          this.toggleFieldClass({ elementId: "card-number", toggleClassesObject: event });

          if (event.error && event.touched) {
            cardNumberError.textContent = event.error;
          } else {
            cardNumberError.textContent = "";
          }
        });

        /**
         * Create expiry date input
         */
        var expiryDate = mollie.createComponent("expiryDate", options);
        expiryDate.mount("#expiry-date");

        var expiryDateError = document.getElementById("expiry-date-error");

        expiryDate.addEventListener("change", function (event) {
          this.toggleFieldClass({ elementId: "expiry-date", toggleClassesObject: event });

          if (event.error && event.touched) {
            expiryDateError.textContent = event.error;
          } else {
            expiryDateError.textContent = "";
          }
        });

        /**
         * Create verification code input
         */
        var verificationCode = mollie.createComponent("verificationCode", options);
        verificationCode.mount("#verification-code");

        var verificationCodeError = document.getElementById("verification-code-error");

        verificationCode.addEventListener("change", function (event) {
          this.toggleFieldClass({
            elementId: "verification-code",
            toggleClassesObject: event,
          });

          if (event.error && event.touched) {
            verificationCodeError.textContent = event.error;
          } else {
            verificationCodeError.textContent = "";
          }
        });
    }
    protected  toggleFieldClass(elementClassObj) {
  var element = document.getElementById(elementClassObj.elementId);

  Object.keys(elementClassObj.toggleClassesObject).forEach(function (key) {
    if (typeof elementClassObj.toggleClassesObject[key] !== "boolean") {
      return;
    }

    if (elementClassObj.toggleClassesObject[key]) {
      element.parentNode.classList.add("is-" + key);
    } else {
      element.parentNode.classList.remove("is-" + key);
    }
  });
}


    protected mapEvents() {
        this.init();
    }
}
