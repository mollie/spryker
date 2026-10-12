import './mollie-apple-pay-direct-component';

import register from 'ShopUi/app/registry';
export default register(
    'mollie-apple-pay-direct-component',
    () =>
        import(
            /* webpackMode: "eager" */
            './mollie-apple-pay-direct-component'
        ),
);
