# 🛒 Mollie Payment Integration for Spryker

This guide provides comprehensive instructions for integrating Mollie payment services with the Spryker Commerce Platform. Mollie is a Payment Service Provider (PSP) that supports multiple payment methods including credit cards, PayPal, bank transfers, and digital wallets.

---

## 📋 Table of Contents

- [1. Prerequisites](#1-prerequisites)
- [2. Setup & Configuration](#2-setup--configuration)
  - [Complete Configuration Structure](#complete-configuration-structure)
  - [2.1 API Key Configuration](#21-api-key-configuration)
  - [2.2 Profile ID Configuration](#22-profile-id-configuration)
  - [2.3 Environment Settings](#23-environment-settings)
  - [2.4 URL Configuration](#24-url-configuration)
- [3. Payment Methods Configuration](#3-payment-methods-configuration)
- [4. Backoffice Configuration](#4-backoffice-configuration)
- [5. Credit Card Components](#5-credit-card-components)
- [6. Wallet Payments](#6-wallet-payments)
- [7. Testing & Debugging](#7-testing--debugging)
- [8. Production Deployment](#8-production-deployment)
- [9. Troubleshooting](#9-troubleshooting)
- [10. Webhook Handling](#10-webhook-handling)
- [11. Webhook Error Troubleshooting](#11-webhook-error-troubleshooting)
- [Getting Help](#getting-help)

---

## 1. Prerequisites

Before starting the integration, ensure you have the following:

- ✅ Active Mollie merchant account ([Sign up here](https://www.mollie.com/dashboard/signup))
- ✅ Mollie API credentials (API key and Profile ID)
- ✅ Spryker Commerce Platform installed and configured
- ✅ Access to Spryker configuration files
- ✅ SSL certificate for production environment (required for webhooks)
- ✅ Basic authentication credentials for test environment

> ⚠️ **Important Security Note**
>
> Never commit API keys or credentials directly to version control. Always use environment variables or secure secret management systems.

---

## 2. Setup & Configuration

All Mollie configuration is managed through the Spryker configuration file, typically located at:

```
config/Shared/config_default.php
```

### Complete Configuration Structure

```php
$config[MollieConstants::MOLLIE] = [
    MollieConstants::MOLLIE_PROFILE_ID => 'profile_id_example',
    MollieConstants::MOLLIE_TEST_MODE => true,
    MollieConstants::MOLLIE_API_KEY => getenv('MOLLIE_API_KEY') ?: '',
    MollieConstants::MOLLIE_DEBUG_MODE => 'Extensive',
    MollieConstants::MOLLIE_REDIRECT_URL => sprintf('https://%s%s', $sprykerFrontendHost, '/checkout/payment-redirect'),
    MollieConstants::MOLLIE_WEBHOOK_URL => sprintf('https://%s%s', $sprykerFrontendHost, '/mollie/webhook'),
    MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED => true,
    MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC => 'https://js.mollie.com/v1/mollie.js',
    MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
        'http://%s:%s@%s%s',
        getenv('SPRYKER_YVES_AUTH_USERNAME') ?? null,
        getenv('SPRYKER_YVES_AUTH_PASSWORD') ?? null,
        $sprykerFrontendHost,
        '/mollie/webhook',
    ),
    MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING => [
        'mollieCreditCardPayment' => 'creditcard',
        'molliePayPalPayment' => 'paypal',
        'mollieBankTransferPayment' => 'banktransfer',
        'mollieKlarnaPayment' => 'klarna',
        'mollieKlarnaPayLaterPayment' => 'klarna',
        'mollieKlarnaPayNowPayment' => 'klarna',
        'mollieKlarnaSliceItPayment' => 'klarna',
        'mollieEpsPayment' => 'eps',
        'mollieIdealPayment' => 'ideal',
        'mollieBancontactPayment' => 'bancontact',
        'mollieKbcPayment' => 'kbc',
        'molliePayByBankPayment' => 'paybybank',
        'mollieApplePayPayment' => 'applepay',
    ],
    MollieConstants::MOLLIE_INCLUDE_WALLETS => ['applepay'],
];
```

---

### 2.1 API Key Configuration

The Mollie API key is the primary authentication mechanism for all API requests.

**Configuration Parameter:**

```php
MollieConstants::MOLLIE_API_KEY => getenv('MOLLIE_API_KEY') ?: ''
```

**Setup Steps:**

1. Log in to your Mollie Dashboard
2. Navigate to **Developers → API Keys**
3. Copy your Test API key (starts with `test_`) or Live API key (starts with `live_`)
4. Add the key to your parameter store:

```bash
MOLLIE_API_KEY='test_YourApiKeyHere'
```

> 🔑 **Security Best Practice**
>
> Store API keys as environment variables. The configuration uses `getenv('MOLLIE_API_KEY')` to retrieve the key securely without exposing it in your codebase.

---

### 2.2 Profile ID Configuration

The Profile ID identifies your Mollie payment profile and is required for operations like fetching available payment methods.

**Configuration Parameter:**

```php
MollieConstants::MOLLIE_PROFILE_ID => 'profile_id_example'
```

**How to Find Your Profile ID:**

1. Log in to your Mollie Dashboard
2. Navigate to **Developers → API Keys**
3. The Profile ID is displayed at the bottom (format: `pfl_xxxxxxxxxx`)

> 💡 **Profile ID Format**
>
> Profile IDs always start with `pfl_` followed by 10 alphanumeric characters.
> Example: `pfl_pD5UernhAv`

---

### 2.3 Environment Settings

**Test Mode**

```php
MollieConstants::MOLLIE_TEST_MODE => true
```

| Value | Description | Use Case |
|---|---|---|
| `true` | Use test mode with test API credentials | Development, staging, testing |
| `false` | Use production mode with live API credentials | Production environment |

**Test Mode Benefits:**

- No actual charges occur
- Use test payment methods and test cards
- Simulate different payment scenarios (success, failure, cancellation)
- Safe environment for integration testing

**Debug Mode**

```php
MollieConstants::MOLLIE_DEBUG_MODE => 'Extensive'
```

| Value | Log Level | What Gets Logged |
|---|---|---|
| `'Off'` | No logging | — |
| `'Basic'` | Essential information only | Request URL, status code, timestamps, high-level events |
| `'Extensive'` | Comprehensive | All API requests, responses, internal operations, state changes |

**Recommended Debug Settings:**

- Development/Testing: `'Extensive'` for full visibility
- Staging: `'Basic'` for error tracking
- Production: `'Basic'` or `'None'` for performance

---

### 2.4 URL Configuration

The integration requires three URLs for payment processing:

#### 1. Redirect URL

Where customers are returned after completing payment on the Mollie payment page.

```php
MollieConstants::MOLLIE_REDIRECT_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/checkout/payment-redirect'
)
```

Example: `https://www.yourstore.com/checkout/payment-redirect`

#### 2. Webhook URL (Production)

Where Mollie sends payment status updates asynchronously.

```php
MollieConstants::MOLLIE_WEBHOOK_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

Example: `https://www.yourstore.com/mollie/webhook`

**Webhook Requirements:**

- Must use HTTPS (SSL certificate required) in production environment
- Must be publicly accessible from the internet
- Should respond with HTTP 200 status code
- Response time should be under 10 seconds

#### 3. Test Environment Webhook URL

For development/staging environments where webhooks need to be accessible to Mollie.

Choose the configuration that matches your environment setup:

**Option A: With HTTP Basic Authentication (If Using .htaccess or Similar)**

If your test environment uses HTTP basic authentication to protect endpoints:

```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'http://%s:%s@%s%s',
    getenv('SPRYKER_YVES_AUTH_USERNAME') ?? null,
    getenv('SPRYKER_YVES_AUTH_PASSWORD') ?? null,
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

Example: `http://username:password@test.yourstore.com/mollie/webhook`

Set credentials via environment variables:

```bash
SPRYKER_YVES_AUTH_USERNAME='your_username'
SPRYKER_YVES_AUTH_PASSWORD='your_password'
```

**Option B: Without Basic Authentication (Publicly Accessible Test Environment)**

If your test environment is publicly accessible without basic authentication:

```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

Example: `https://test.yourstore.com/mollie/webhook`

> 📋 **Configuration Guidance**
>
> **Use Option A when:**
> - Your staging/test environment uses `.htaccess` protection
> - You have HTTP basic authentication enabled via web server configuration
> - You want to restrict public access to test endpoints
>
> **Use Option B when:**
> - Your test environment is publicly accessible
> - You use alternative security methods (IP whitelisting, VPN, etc.)
> - Your test server has HTTPS configured
>
> **Security Note:** If using Option B without basic auth, consider implementing alternative security measures such as IP whitelisting for Mollie's webhook servers or restricting access via firewall rules.

**HTTPS vs HTTP for Test Environments**

- **HTTPS (Recommended):** Use `https://` if your test environment has a valid SSL certificate
- **HTTP:** Only use `http://` for local development or internal networks not accessible from the internet

HTTPS Configuration Example:

```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

---

## 3. Payment Methods Configuration

The OMS (Order Management System) mapping connects Spryker payment method names to Mollie payment method identifiers.

### Payment Method Mapping

```php
MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING => [
    'mollieCreditCardPayment' => 'creditcard',
    'molliePayPalPayment' => 'paypal',
    'mollieBankTransferPayment' => 'banktransfer',
    'mollieKlarnaPayment' => 'klarna',
    'mollieKlarnaPayLaterPayment' => 'klarna',
    'mollieKlarnaPayNowPayment' => 'klarna',
    'mollieKlarnaSliceItPayment' => 'klarna',
    'mollieEpsPayment' => 'eps',
    'mollieIdealPayment' => 'ideal',
    'mollieBancontactPayment' => 'bancontact',
    'mollieKbcPayment' => 'kbc',
    'molliePayByBankPayment' => 'paybybank',
    'mollieApplePayPayment' => 'applepay',
]
```

### Supported Payment Methods

| Spryker Payment Method | Mollie ID | Description | Region |
|---|---|---|---|
| `mollieCreditCardPayment` | `creditcard` | Credit/Debit Cards (Visa, Mastercard, Amex) | Global |
| `molliePayPalPayment` | `paypal` | PayPal wallet | Global |
| `mollieBankTransferPayment` | `banktransfer` | Direct bank transfer | Europe |
| `mollieKlarnaPayment` | `klarna` | Klarna Pay Later / Financing | Europe, US |
| `mollieIdealPayment` | `ideal` | iDEAL bank payment | Netherlands |
| `mollieBancontactPayment` | `bancontact` | Bancontact card payment | Belgium |
| `mollieEpsPayment` | `eps` | EPS bank transfer | Austria |
| `mollieKbcPayment` | `kbc` | KBC/CBC Payment Button | Belgium |
| `molliePayByBankPayment` | `paybybank` | Open Banking payments | UK |
| `mollieApplePayPayment` | `applepay` | Apple Pay digital wallet | Global |

---

## 4. Backoffice Configuration

The Mollie integration provides a dedicated panel in the Spryker Backoffice where you can view all enabled payment methods and their configuration status.

### Displaying the Mollie Panel in Back Office

#### Step 1: Update Navigation Configuration

Edit the navigation configuration file located at:

```
config/Zed/navigation.xml
```

Add the following configuration block:

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

> ⚠️ **Important:** Insert this block between the `<payment-method>` and `<shipment-method>` nodes in the navigation file.

Example placement:

```xml
<navigation>
    <!-- ... other navigation items ... -->
    <payment-method>
        <label>Payment Methods</label>
        <!-- payment method config -->
    </payment-method>
    <!-- INSERT MOLLIE CONFIG HERE -->
    <mollie-payment-methods>
        <label>Mollie payment methods</label>
        <title>Mollie payment methods</title>
        <bundle>mollie</bundle>
        <controller>index</controller>
        <action>index</action>
        <visible>1</visible>
    </mollie-payment-methods>
    <shipment-method>
        <label>Shipment Methods</label>
        <!-- shipment method config -->
    </shipment-method>
    <!-- ... other navigation items ... -->
</navigation>
```

#### Step 2: Rebuild Navigation Cache

After updating the `navigation.xml` file, run these commands to apply the changes:

```bash
console navigation:cache:remove
console navigation:build-cache
```

#### Step 3: Configure Translations

To enable proper translation of Zed glossary keys, update the translator configuration:

**File location:** `Pyz/Zed/Translator/TranslatorConfig.php`

Add the following function to the `TranslatorConfig` class:

```php
/**
 * @return array|string[]
 */
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

#### Step 4: Clear Translation Cache (if applicable)

```bash
console cache:clear
```

### Accessing the Mollie Panel

After completing the configuration:

1. Log in to your Spryker Backoffice
2. Navigate to **Administration > Mollie payment methods** in the main navigation menu

The panel displays:

- All payment methods from your Mollie account
- Button for showing only enabled payment methods
- Button for clearing payment method cache
- Payment method status (active/inactive)
- Minimum/maximum transaction amount
- Payment method icons

### What You Can See in the Back Office Panel
<img src="../.github/images/payment_methods.png" alt="Mollie Payment Methods Backoffice Panel" width="600" />

| Information | Description |
|---|---|
| Payment Method Name | Display name (e.g., "Credit Card", "PayPal", "iDEAL") |
| Status | "Activated" or "Not Activated" in your Mollie account |
| Minimum Amount | Minimum transaction amount for this method |
| Maximum Amount | Maximum transaction amount for this method |
| Images | Payment method icons |

> 🔄 **Real-Time Sync**
>
> The Back Office panel fetches payment method information directly from the Mollie API, ensuring you always see the current configuration from your Mollie Dashboard.

### Troubleshooting Back Office Panel

**Panel not appearing in navigation:**

- Verify the XML configuration is in the correct location
- Ensure the XML is properly formatted (no syntax errors)
- Check that navigation cache was rebuilt
- Clear browser cache and refresh

**Payment methods not loading:**

- Verify API key is configured correctly
- Check that `MOLLIE_TEST_MODE` matches your API key type (test/live)
- Review debug logs for API errors
- Ensure Profile ID is correct

**Translation keys showing instead of labels:**

- Verify translator configuration is added to `TranslatorConfig.php`
- Check that translation CSV files are present in the Mollie module
- Clear application cache
- Rebuild translations if needed

---

## 5. Credit Card Components

Mollie provides secure, embeddable credit card components that allow customers to enter card details directly on your checkout page without the data passing through your servers.

### Enabling Components

```php
MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED => true
```

### JavaScript Library Configuration

```php
MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC => 'https://js.mollie.com/v1/mollie.js'
```

### Benefits

| Benefit | Description |
|---|---|
| 🔒 Enhanced Security | Card details never touch your servers, reducing PCI compliance requirements |
| 🎯 Reduced PCI Scope | SAQ-A compliance instead of SAQ-D |
| 🚀 Seamless UX | No redirect, customers stay on your checkout page |
| ✅ Built-in Validation | Real-time card validation and formatting |
| 🛡️ 3D Secure Support | Automatic SCA (Strong Customer Authentication) handling |

### Implementation Requirements

- HTTPS is required for components to function
- Components must be initialized with your Mollie Profile ID
- The integration handles tokenization automatically

### Component Example
<img src="../.github/images/credit_card_component.png" alt="Credit Card Component" width="600" />

---

## 6. Wallet Payments

Digital wallet payments provide customers with quick, one-click checkout options using stored payment credentials.

### Wallet Configuration

```php
MollieConstants::MOLLIE_INCLUDE_WALLETS => ['applepay']
```

### Apple Pay Integration

Apple Pay allows customers to pay using Face ID, Touch ID, or passcode on supported Apple devices.

**Setup Steps:**

1. Enable Apple Pay in your Mollie Dashboard
2. Add your domain for Apple Pay verification
3. Download and host the verification file on your domain
4. Complete the verification process
5. Add `'applepay'` to the `MOLLIE_INCLUDE_WALLETS` array
6. Test on compatible devices

> 📱 **Automatic Device Detection**
>
> Apple Pay will only display as a payment option when accessed from compatible devices and browsers. The integration automatically handles device and browser detection.

---

## 7. Testing & Debugging

### Test Mode Setup

1. Set `MOLLIE_TEST_MODE` to `true`
2. Use your test API key (starts with `test_`)
3. Enable extensive debug logging: `MOLLIE_DEBUG_MODE => 'Extensive'`

### Test Credit Cards

| Card Number | Result |
|---|---|
| `5555 5555 5555 4444` | ✅ Success |
| `4242 4242 4242 4242` | ✅ Success |
| `4111 1111 1111 1111` | ✅ Success |

**Test Card Details:**

- Expiry Date: Any future date
- CVC: Any 3 digits
- Cardholder Name: Any name

### Debug Logging

The integration provides comprehensive logging capabilities based on the configured debug mode.

#### How to Enable Debug Mode

**Step 1: Update Configuration**

Edit your Spryker configuration file (`config/Shared/config_default.php` or environment-specific config):

```php
$config[MollieConstants::MOLLIE_DEBUG_MODE] = 'Extensive';
```

**Step 2: Verify Logging is Active**

Perform a test transaction and check that logs are being written.

#### Log Levels and Content

- **Off Mode:** No logging (not recommended)
- **Basic Mode Logs:**
  - Request URLs and HTTP methods
  - Error messages and codes
- **Extensive Mode Logs** (includes all Basic logs plus):
  - Complete request/response bodies (with sensitive data masked)
  - Error messages and codes

### Sensitive Data Masking

When `MOLLIE_DEBUG_MODE` is set to `'Extensive'`, sensitive data is automatically masked:

| Field | Masked Example |
|---|---|
| API Keys | `test_abc*********************` |
| Email | `john.doe@*****.com` |
| Phone | `*******1234` |
| Names | `John D.` |

### Common Test Issues

| Issue | Possible Cause | Solution |
|---|---|---|
| Payment methods not appearing | Methods not enabled in Mollie Dashboard | Enable payment methods in test mode |
| Webhook not receiving updates | URL not accessible or incorrect | Test webhook URL manually, check firewall |
| API errors | Invalid API key or Profile ID | Verify credentials in Mollie Dashboard |

---

## 8. Production Deployment

### Pre-Production Checklist

- [ ] Switch to live API key (use `live_` API key)
- [ ] Set test mode to false (`MOLLIE_TEST_MODE => false`)
- [ ] Reduce debug logging (set to `'Basic'` or `'None'`)
- [ ] Test production payments with small transactions
- [ ] Configure monitoring and alerts

### Production Configuration

```php
$config[MollieConstants::MOLLIE] = [
    MollieConstants::MOLLIE_PROFILE_ID => 'pfl_YourLiveProfileID',
    MollieConstants::MOLLIE_TEST_MODE => false,  // Production mode
    MollieConstants::MOLLIE_API_KEY => getenv('MOLLIE_API_KEY') ?: '',  // Live key
    MollieConstants::MOLLIE_DEBUG_MODE => 'Basic',  // Reduced logging
    // ... rest of configuration
];
```

> 🔴 **Production safety**
>
> - Never use test API keys in production
> - Always use HTTPS for all production URLs
> - Implement proper error handling and logging

---

## 9. Troubleshooting

### Payment methods not displaying at checkout

**Possible Causes:**

- Payment methods not enabled in Mollie Dashboard
- Incorrect Profile ID
- API key issues
- OMS mapping misconfiguration

**Solutions:**

- Verify payment methods are enabled in Mollie Dashboard (**Settings → Payment Methods**)
- Check that your Profile ID is correct
- Verify API key has correct permissions
- Check debug logs for API errors

### Webhooks not being received

**Possible Causes:**

- Webhook URL not accessible
- Firewall blocking Mollie's servers
- SSL certificate issues

**Solutions:**

- Test webhook URL manually from external location
- Whitelist Mollie's IP ranges in your firewall
- Check SSL certificate validity
- Review server logs for incoming webhook requests

### Credit card components not loading

**Possible Causes:**

- JavaScript library not loading
- HTTPS not enabled
- Content Security Policy (CSP) blocking
- Components not enabled in configuration

**Solutions:**

- Ensure site is running on HTTPS
- Check browser console for JavaScript errors
- Update CSP to allow `js.mollie.com`
- Verify Profile ID is correct

### Apple Pay not appearing

**Possible Causes:**

- Domain not verified with Apple
- Apple Pay not enabled in Mollie Dashboard
- Missing from wallet configuration

**Solutions:**

- Complete Apple Pay domain verification in Mollie Dashboard
- Verify Apple Pay is enabled for your account
- Test on compatible Apple device with Safari or Chrome
- Check that Apple Pay is enabled in device settings

---

## 10. Webhook Handling

Webhooks are asynchronous notifications sent by Mollie to your server when payment status changes occur. Proper webhook handling is critical for accurate order processing and payment status updates.

### How Webhooks Work

1. **Payment Status Changes:** When a payment status changes (e.g., paid, cancelled, expired), Mollie sends an HTTP POST request to your webhook URL
2. **Webhook Receipt:** Your server receives the webhook notification
3. **Validation:** The webhook payload is validated to ensure it's from Mollie
4. **Status Retrieval:** Your server fetches the latest payment details from Mollie API
5. **Order Update:** The order status is updated based on the payment status
6. **Response:** Your server responds with HTTP 200 to acknowledge receipt

### Webhook Configuration

**Production:**

```php
MollieConstants::MOLLIE_WEBHOOK_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

**Test Environment:**

```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

### Webhook Payload Structure

When Mollie sends a webhook, it includes the payment ID in the POST body:

```
POST /mollie/webhook HTTP/1.1
Host: www.yourstore.com
Content-Type: application/x-www-form-urlencoded

id=tr_WDqYK6vllg
```

> ⚠️ **Important:** The webhook contains only the payment ID. Your application must then make an API call to Mollie to retrieve the full payment details.

### Payment Status Mapping

Mollie payment statuses map to OMS states as follows:

| Mollie Status | Description | OMS Action | Typical Next State |
|---|---|---|---|
| `open` | Payment created, awaiting customer action | None | `payment_pending` |
| `pending` | Payment started, awaiting confirmation | None | `payment_pending` |
| `authorized` | Payment authorized, awaiting capture | Authorize recorded | `payment_authorized` |
| `paid` | Payment successfully completed | Mark as paid | `payment_completed` |
| `failed` | Payment failed | Mark as failed | `payment_failed` |
| `canceled` | Payment cancelled by customer | Mark as cancelled | `payment_cancelled` |
| `expired` | Payment expired (timeout) | Mark as expired | `payment_expired` |

### Webhook Retry Behavior

If your server doesn't respond with HTTP 200, Mollie will retry the webhook:

**Retry Schedule:**

- Immediate retry
- After 15 minutes
- After 30 minutes
- After 1 hour
- After 3 hours
- After 6 hours
- After 12 hours
- After 24 hours

**Total Retry Period:** Up to 24 hours

> 💡 **Best Practice**
>
> Always respond with HTTP 200 immediately, even if processing hasn't completed. Process the webhook asynchronously if needed to avoid timeouts.

---

## 11. Webhook Error Troubleshooting

### Common Webhook Issues

#### Issue 1: Webhooks Not Being Received

**Symptoms:**

- Payment status not updating in your system
- Orders stuck in "payment pending" state
- No webhook logs in application logs

**Diagnostic Steps:**

1. Check Webhook URL Configuration:

```bash
# Verify webhook URL is set correctly
MOLLIE_WEBHOOK_URL config/Shared/config_default.php
```

2. Test URL Accessibility:

```bash
# Test from external location
curl -X POST https://www.yourstore.com/mollie/webhook \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "id=tr_test123"
```

Expected: HTTP 200 response

3. Check Firewall Rules:
   - Ensure Mollie's IP addresses are not blocked
   - Check cloud provider security groups
   - Verify web application firewall (WAF) rules

**Common Causes & Solutions:**

| Cause | Solution |
|---|---|
| Firewall blocking Mollie IPs | Whitelist Mollie's IP ranges in firewall |
| Invalid SSL certificate | Renew or fix SSL certificate |
| Webhook URL typo | Verify URL spelling and path |
| Basic auth blocking webhooks | Add credentials to webhook URL or disable auth |
| Server not publicly accessible | Ensure server is reachable from internet |
| DNS issues | Verify domain resolves correctly |

#### Issue 2: Webhooks Received But Not Processed

**Symptoms:**

- Webhook logs show receipt but no order updates
- Error logs show processing failures
- HTTP 500 responses to webhooks

**Diagnostic Steps:**

1. Enable Extensive Logging:

```php
MollieConstants::MOLLIE_DEBUG_MODE => 'Extensive'
```

2. Check Application Logs

3. Review Error Details

4. Test Payment Status API:

```bash
curl -X GET https://api.mollie.com/v2/payments/tr_WDqYK6vllg \
  -H "Authorization: Bearer test_yourapikey"
```

**Common Causes & Solutions:**

| Cause | Solution |
|---|---|
| Invalid API key | Verify API key in environment variables |
| Database connection failure | Check database connectivity |
| OMS state machine error | Review OMS configuration for payment method |
| Order not found | Verify order exists and payment ID mapping |
| Timeout during processing | Implement async processing |
| PHP errors / exceptions | Review error logs and fix code issues |

### Getting Help with Webhook Issues

When contacting support, provide:

- **Correlation ID:** Specific transaction identifier
- **Payment ID:** Mollie payment ID (`tr_*`)
- **Timestamp:** When issue occurred
- **Logs:** Relevant log entries with correlation ID
- **Configuration:** Current webhook URL configuration
- **Test Results:** Results of manual webhook tests
- **Environment:** Production, staging, or development

**Mollie Support Resources:**

- Technical Documentation: [docs.mollie.com/overview/webhooks](https://docs.mollie.com/overview/webhooks)
- API Status: [status.mollie.com](https://status.mollie.com)
- Support Portal: [help.mollie.com](https://help.mollie.com)

---

## Getting Help

| Resource | Link | Use For |
|---|---|---|
| Mollie API Documentation | [docs.mollie.com](https://docs.mollie.com) | API reference, payment methods, features |
| Mollie Support | [help.mollie.com](https://help.mollie.com) | Account issues, payment method questions |
| Spryker Documentation | [docs.spryker.com](https://docs.spryker.com) | Spryker platform, OMS configuration |