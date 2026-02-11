import './mollie-apple-pay-component';

import register from 'ShopUi/app/registry';
export default register(
    'mollie-apple-pay-component',
    () =>
        import(
            /* webpackMode: "eager" */
            './mollie-apple-pay-component'
            ),
);
