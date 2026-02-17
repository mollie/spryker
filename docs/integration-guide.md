# 🛒 Mollie Payment Integration for Spryker

A comprehensive integration of [Mollie](https://www.mollie.com) payment services with the [Spryker Commerce Platform](https://spryker.com), supporting credit cards, PayPal, bank transfers, digital wallets, and more.

---

## 📋 Table of Contents

- [Prerequisites](#prerequisites)
- [Setup & Configuration](#setup--configuration)
  - [API Key](#api-key-configuration)
  - [Profile ID](#profile-id-configuration)
  - [Environment Settings](#environment-settings)
  - [URL Configuration](#url-configuration)
- [Payment Methods](#payment-methods)
- [Back Office Panel](#back-office-panel)
- [Credit Card Components](#credit-card-components)
- [Wallet Payments](#wallet-payments)
- [Testing & Debugging](#testing--debugging)
- [Production Deployment](#production-deployment)
- [Webhook Handling](#webhook-handling)
- [Troubleshooting](#troubleshooting)

---

## ✅ Prerequisites

Before starting, ensure you have:

- [ ] Active [Mollie merchant account](https://www.mollie.com/dashboard/signup)
- [ ] Mollie API credentials (API key + Profile ID)
- [ ] Spryker Commerce Platform installed and configured
- [ ] Access to Spryker configuration files
- [ ] SSL certificate for production (required for webhooks)
- [ ] Basic auth credentials for test environment (if applicable)

> ⚠️ **Security Notice:** Never commit API keys or credentials to version control. Always use environment variables or a secure secrets manager.

---

## ⚙️ Setup & Configuration

All configuration lives in:

```
config/Shared/config_default.php
```

### Complete Configuration Example

```php
$config[MollieConstants::MOLLIE] = [
    MollieConstants::MOLLIE_PROFILE_ID                     => 'pfl_YourProfileIdHere',
    MollieConstants::MOLLIE_TEST_MODE                      => true,
    MollieConstants::MOLLIE_API_KEY                        => getenv('MOLLIE_API_KEY') ?: '',
    MollieConstants::MOLLIE_DEBUG_MODE                     => 'Extensive',
    MollieConstants::MOLLIE_REDIRECT_URL                   => sprintf('https://%s/checkout/payment-redirect', $sprykerFrontendHost),
    MollieConstants::MOLLIE_WEBHOOK_URL                    => sprintf('https://%s/mollie/webhook', $sprykerFrontendHost),
    MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED => true,
    MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC  => 'https://js.mollie.com/v1/mollie.js',
    MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL   => sprintf(
        'http://%s:%s@%s/mollie/webhook',
        getenv('SPRYKER_YVES_AUTH_USERNAME') ?? null,
        getenv('SPRYKER_YVES_AUTH_PASSWORD') ?? null,
        $sprykerFrontendHost,
    ),
    MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING  => [
        'mollieCreditCardPayment'   => 'creditcard',
        'molliePayPalPayment'       => 'paypal',
        'mollieBankTransferPayment' => 'banktransfer',
        'mollieKlarnaPayment'       => 'klarna',
        'mollieIdealPayment'        => 'ideal',
        'mollieBancontactPayment'   => 'bancontact',
        'mollieEpsPayment'          => 'eps',
        'mollieKbcPayment'          => 'kbc',
        'molliePayByBankPayment'    => 'paybybank',
        'mollieApplePayPayment'     => 'applepay',
    ],
    MollieConstants::MOLLIE_INCLUDE_WALLETS => ['applepay'],
];
```

---

### API Key Configuration

```php
MollieConstants::MOLLIE_API_KEY => getenv('MOLLIE_API_KEY') ?: ''
```

1. Log in to your [Mollie Dashboard](https://www.mollie.com/dashboard)
2. Go to **Developers → API Keys**
3. Copy your **Test** (`test_...`) or **Live** (`live_...`) key
4. Set it as an environment variable:

```bash
MOLLIE_API_KEY='test_YourApiKeyHere'
```

---

### Profile ID Configuration

```php
MollieConstants::MOLLIE_PROFILE_ID => 'pfl_YourProfileIdHere'
```

Find your Profile ID in **Developers → API Keys** on the Mollie Dashboard. It always starts with `pfl_` followed by 10 alphanumeric characters (e.g. `pfl_pD5UernhAv`).

---

### Environment Settings

| Setting | Value | Description |
|---|---|---|
| `MOLLIE_TEST_MODE` | `true` | Development / staging — no real charges |
| `MOLLIE_TEST_MODE` | `false` | Production — live payments |

**Debug Mode options:**

| Value | Logs |
|---|---|
| `'Off'` | No logging |
| `'Basic'` | Request URLs, status codes, high-level events |
| `'Extensive'` | Full request/response bodies (sensitive data masked) |

> **Recommendation:** Use `'Extensive'` in development, `'Basic'` in staging, and `'Basic'` or `'Off'` in production.

---

### URL Configuration

**Redirect URL** — where customers land after payment:
```php
MollieConstants::MOLLIE_REDIRECT_URL => 'https://www.yourstore.com/checkout/payment-redirect'
```

**Webhook URL (Production)** — for asynchronous payment status updates:
```php
MollieConstants::MOLLIE_WEBHOOK_URL => 'https://www.yourstore.com/mollie/webhook'
```

**Test Environment Webhook URL:**

_With HTTP Basic Auth (e.g. `.htaccess` protected):_
```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'http://%s:%s@%s/mollie/webhook',
    getenv('SPRYKER_YVES_AUTH_USERNAME'),
    getenv('SPRYKER_YVES_AUTH_PASSWORD'),
    $sprykerFrontendHost,
)
```

_Without Basic Auth (publicly accessible):_
```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'https://%s/mollie/webhook',
    $sprykerFrontendHost,
)
```

---

## 💳 Payment Methods

| Spryker Method | Mollie ID | Description | Region |
|---|---|---|---|
| `mollieCreditCardPayment` | `creditcard` | Visa, Mastercard, Amex | Global |
| `molliePayPalPayment` | `paypal` | PayPal wallet | Global |
| `mollieBankTransferPayment` | `banktransfer` | Direct bank transfer | Europe |
| `mollieKlarnaPayment` | `klarna` | Klarna Pay Later / Financing | Europe, US |
| `mollieIdealPayment` | `ideal` | iDEAL | Netherlands |
| `mollieBancontactPayment` | `bancontact` | Bancontact card | Belgium |
| `mollieEpsPayment` | `eps` | EPS bank transfer | Austria |
| `mollieKbcPayment` | `kbc` | KBC/CBC Payment Button | Belgium |
| `molliePayByBankPayment` | `paybybank` | Open Banking | UK |
| `mollieApplePayPayment` | `applepay` | Apple Pay | Global |

---

## 🖥️ Back Office Panel

The integration adds a dedicated **Mollie Payment Methods** panel to your Spryker Back Office.

### Step 1 — Update Navigation Config

Edit `config/Zed/navigation.xml` and insert between the `<payment-method>` and `<shipment-method>` nodes:

```xml
<mollie-payment-methods>
    <label>Mollie payment methods</label>
    <title>Mollie payment methods</title>
    <bundle>mollie</bundle>
    <controller>index</controller>
    <action>index</action>
    <visible>1</visible>
</mollie-payment-methods>
```

### Step 2 — Rebuild Navigation Cache

```bash
console navigation:cache:remove
console navigation:build-cache
```

### Step 3 — Configure Translations

In `Pyz/Zed/Translator/TranslatorConfig.php`, add:

```php
public function getCoreTranslationFilePathPatterns(): array
{
    return array_merge(
        parent::getCoreTranslationFilePathPatterns(),
        [
            APPLICATION_VENDOR_DIR . '/mollie/*/data/translation/Zed/[a-z][a-z]_[A-Z][A-Z].csv',
        ],
    );
}
```

### Step 4 — Clear Cache

```bash
console cache:clear
```

The panel is then accessible at **Administration → Mollie payment methods** and shows real-time status, min/max transaction limits, and icons for all configured methods.

---

## 🔐 Credit Card Components

Mollie's embeddable credit card components allow customers to enter card details directly on your checkout page — card data never touches your servers.

```php
MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED => true,
MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC  => 'https://js.mollie.com/v1/mollie.js',
```

| Benefit | Details |
|---|---|
| 🔒 Enhanced Security | Card data never passes through your servers |
| 📋 Reduced PCI Scope | SAQ-A compliance instead of SAQ-D |
| 🚀 Seamless UX | Customers stay on your checkout page |
| ✅ Built-in Validation | Real-time card formatting & validation |
| 🛡️ 3DS Support | Automatic SCA / Strong Customer Authentication |

> **Requirement:** HTTPS is required for components to function.

---

## 📱 Wallet Payments

### Apple Pay

```php
MollieConstants::MOLLIE_INCLUDE_WALLETS => ['applepay']
```

**Setup steps:**

1. Enable Apple Pay in your Mollie Dashboard
2. Add and verify your domain
3. Download and host the verification file
4. Add `'applepay'` to `MOLLIE_INCLUDE_WALLETS`
5. Test on a compatible Apple device

> Apple Pay automatically detects device/browser compatibility and only displays on supported configurations.

---

## 🧪 Testing & Debugging

### Test Mode Setup

```php
MollieConstants::MOLLIE_TEST_MODE  => true,
MollieConstants::MOLLIE_API_KEY    => getenv('MOLLIE_API_KEY'), // test_ key
MollieConstants::MOLLIE_DEBUG_MODE => 'Extensive',
```

### Test Credit Cards

| Card Number | Result |
|---|---|
| `5555 5555 5555 4444` | ✅ Success |
| `4242 4242 4242 4242` | ✅ Success |
| `4111 1111 1111 1111` | ✅ Success |

Use any future expiry date, any 3-digit CVC, and any cardholder name.

### Sensitive Data Masking

When `'Extensive'` logging is enabled, sensitive fields are automatically masked:

| Field | Masked Example |
|---|---|
| API Key | `test_abc*********************` |
| Email | `john.doe@*****.com` |
| Phone | `*******1234` |
| Name | `John D.` |

---

## 🚀 Production Deployment

### Pre-Production Checklist

- [ ] Switch to live API key (`live_...`)
- [ ] Set `MOLLIE_TEST_MODE => false`
- [ ] Reduce debug logging to `'Basic'` or `'Off'`
- [ ] Verify SSL certificate is valid
- [ ] Test with small real transactions
- [ ] Configure monitoring and alerts

### Production Configuration

```php
$config[MollieConstants::MOLLIE] = [
    MollieConstants::MOLLIE_PROFILE_ID  => 'pfl_YourLiveProfileID',
    MollieConstants::MOLLIE_TEST_MODE   => false,
    MollieConstants::MOLLIE_API_KEY     => getenv('MOLLIE_API_KEY') ?: '',
    MollieConstants::MOLLIE_DEBUG_MODE  => 'Basic',
    // ... rest of configuration
];
```

---

## 🔔 Webhook Handling

Webhooks notify your server of payment status changes asynchronously.

### How It Works

1. Payment status changes on Mollie's side
2. Mollie sends an HTTP POST to your webhook URL
3. Your server validates and acknowledges with HTTP 200
4. Your server fetches full payment details from the Mollie API
5. Order status is updated accordingly

### Webhook Payload

```
POST /mollie/webhook HTTP/1.1
Content-Type: application/x-www-form-urlencoded

id=tr_WDqYK6vllg
```

> The payload contains only the payment ID — your server must call the Mollie API to retrieve full payment details.

### Payment Status Mapping

| Mollie Status | OMS Action | Resulting State |
|---|---|---|
| `open` | None | `payment_pending` |
| `pending` | None | `payment_pending` |
| `authorized` | Authorize recorded | `payment_authorized` |
| `paid` | Mark as paid | `payment_completed` |
| `failed` | Mark as failed | `payment_failed` |
| `canceled` | Mark as cancelled | `payment_cancelled` |
| `expired` | Mark as expired | `payment_expired` |

### Retry Schedule

If your server doesn't return HTTP 200, Mollie retries over 24 hours: immediately → 15 min → 30 min → 1 hr → 3 hr → 6 hr → 12 hr → 24 hr.

> **Best Practice:** Respond with HTTP 200 immediately, then process the webhook asynchronously to avoid timeouts.

---

## 🛠️ Troubleshooting

### Payment Methods Not Displaying

- Verify methods are enabled in **Mollie Dashboard → Settings → Payment Methods**
- Confirm your Profile ID is correct
- Check API key permissions and test/live mode alignment
- Review debug logs for API errors

### Webhooks Not Being Received

Test your webhook URL manually:
```bash
curl -X POST https://www.yourstore.com/mollie/webhook \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "id=tr_test123"
```

Common causes: firewall blocking Mollie IPs, invalid SSL certificate, URL typo, or server not publicly accessible.

### Credit Card Components Not Loading

- Ensure the site is running on HTTPS
- Check browser console for JS errors
- Update CSP headers to allow `js.mollie.com`
- Verify the Profile ID is correct in configuration

### Apple Pay Not Appearing

- Complete domain verification in the Mollie Dashboard
- Test on a compatible Apple device using Safari or Chrome
- Confirm `'applepay'` is in the `MOLLIE_INCLUDE_WALLETS` array
- Check Apple Pay is enabled in device settings

### Back Office Panel Not Appearing

- Verify XML is correctly placed in `navigation.xml`
- Rebuild navigation cache: `console navigation:build-cache`
- Clear browser cache and refresh

---

## 📚 Resources

| Resource | Link |
|---|---|
| Mollie API Docs | [docs.mollie.com](https://docs.mollie.com) |
| Mollie Webhooks | [docs.mollie.com/overview/webhooks](https://docs.mollie.com/overview/webhooks) |
| Mollie Support | [help.mollie.com](https://help.mollie.com) |
| Mollie API Status | [status.mollie.com](https://status.mollie.com) |
| Spryker Docs | [docs.spryker.com](https://docs.spryker.com) |

---

## 📄 License

This integration is provided under the terms of your organisation's software license. Refer to the `LICENSE` file for details.