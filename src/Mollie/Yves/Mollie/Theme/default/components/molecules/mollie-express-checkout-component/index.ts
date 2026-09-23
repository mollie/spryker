import './mollie-express-checkout-component';

import register from 'ShopUi/app/registry';
export default register(
    'mollie-express-checkout-component',
    () =>
        import(
            /* webpackMode: "eager" */
            './mollie-express-checkout-component'
        ),
);
