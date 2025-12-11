import './mollie-credit-card-component';
import './mollie-credit-card-component.scss';

import register from 'ShopUi/app/registry';
export default register(
    'mollie-credit-card-component',
    () =>
        import(
            /* webpackMode: "eager" */
            './mollie-credit-card-component'
        ),
);
