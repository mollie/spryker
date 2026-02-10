import './mollie-logo-image';
import './mollie-logo-image.scss';

import register from 'ShopUi/app/registry';
export default register(
    'mollie-logo-image',
    () =>
        import(
            /* webpackMode: "eager" */
            './mollie-logo-image'
        ),
);
