import './mollie-apple-pay-component';
import './mollie-apple-pay-component.scss';

import register from 'ShopUi/app/registry';
export default register(
    'mollie-apple-pay-component',
    () =>
        import(
            /* webpackMode: "eager" */
            './mollie-apple-pay-component'
            ),
);
