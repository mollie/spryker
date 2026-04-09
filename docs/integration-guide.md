# Mollie Payment Integration for Spryker - Integration Guide

## Overview

This guide provides comprehensive instructions for integrating Mollie payment services with the Spryker Commerce Platform. Mollie is a Payment Service Provider (PSP) that supports multiple payment methods including credit cards, PayPal, bank transfers, and digital wallets.

## Table of Contents

- [1. Prerequisites](#1-prerequisites)
- [2. Setup & Configuration](#2-setup--configuration)
  - [Complete Configuration Structure](#complete-configuration-structure)
  - [API Key Configuration](#api-key-configuration)
  - [Profile ID Configuration](#profile-id-configuration)
  - [Environment Settings](#environment-settings)
  - [Test Mode](#test-mode)
  - [Debug Mode](#debug-mode)
  - [URL Configuration](#url-configuration)
  - [1. Redirect URL](#1-redirect-url)
  - [2. Webhook URL (Production)](#2-webhook-url-production)
  - [3. Test Environment Webhook URL](#3-test-environment-webhook-url)
  - [Option A: With HTTP Basic Authentication (If Using .htaccess or Similar)](#option-a-with-http-basic-authentication-if-using-htaccess-or-similar)
  - [Option B: Without Basic Authentication (Publicly Accessible Test Environment)](#option-b-without-basic-authentication-publicly-accessible-test-environment)
  - [HTTPS vs HTTP for Test Environments](#https-vs-http-for-test-environments)
- [3. Setting Up Mollie Payment Methods](#3-setting-up-mollie-payment-methods)
  - [3.1. Add Mollie Payment Methods to CSV](#31-add-mollie-payment-methods-to-csv)
  - [Example (all available Mollie methods)](#example-all-available-mollie-methods)
  - [3.2 Assign Payment Methods to Stores](#32-assign-payment-methods-to-stores)
  - [3.3 Import Payment Methods](#33-import-payment-methods)
  - [3.4 Configure OMS → Mollie Payment Mapping](#34-configure-oms--mollie-payment-mapping)
  - [3.5 Add Payment Forms to Twig](#35-add-payment-forms-to-twig)
  - [3.6 Final Steps](#36-final-steps)
  - [3.7 Displaying Payment Method Logos in Checkout](#37-displaying-payment-method-logos-in-checkout)
  - [3.8 Payment Method Overrides (Names, Logos, Minimum and Maximum amounts)](#38-payment-method-overrides-names-logos-minimum-and-maximum-amounts)
- [4. Dependency Provider Configuration](#4-dependency-provider-configuration)
  - [4.1. Router Dependency Provider](#41-router-dependency-provider)
  - [Route Provider Plugin](#route-provider-plugin)
  - [4.2. Checkout Dependency Provider (Zed)](#42-checkout-dependency-provider-zed)
  - [Post Hooks](#post-hooks)
  - [4.3. Checkout Page Dependency Provider (Yves)](#43-checkout-page-dependency-provider-yves)
  - [Payment Method Handlers](#payment-method-handlers)
  - [Subform Plugin Collection](#subform-plugin-collection)
  - [Checkout Summary Step Pre-Condition Plugins](#checkout-summary-step-pre-condition-plugins)
  - [4.4. Payment Dependency Provider](#44-payment-dependency-provider)
  - [Payment Method Filter Plugins](#payment-method-filter-plugins)
  - [4.5. OMS Dependency Provider](#45-oms-dependency-provider)
  - [Command Plugins](#command-plugins)
  - [Condition Plugins](#condition-plugins)
  - [4.6. Mail Dependency Provider](#46-mail-dependency-provider)
  - [Command Plugins](#command-plugins-1)
- [5. Glossary Keys and Translations](#5-glossary-keys-and-translations)
  - [Complete Glossary CSV](#complete-glossary-csv)
- [6. Payment Methods Configuration](#6-payment-methods-configuration)
  - [Payment Method Mapping](#payment-method-mapping)
  - [Supported Payment Methods](#supported-payment-methods)
- [7. Backoffice Configuration](#7-backoffice-configuration)
  - [Displaying the Mollie Panel in Back Office](#displaying-the-mollie-panel-in-back-office)
  - [Step 1: Update Navigation Configuration](#step-1-update-navigation-configuration)
  - [Step 2: Rebuild Navigation Cache](#step-2-rebuild-navigation-cache)
  - [Step 3: Configure Translations](#step-3-configure-translations)
  - [Step 4: Clear Translation Cache (if applicable)](#step-4-clear-translation-cache-if-applicable)
  - [Accessing the Mollie Panel](#accessing-the-mollie-panel)
  - [What You Can See in the Back Office Panel](#what-you-can-see-in-the-back-office-panel)
  - [Troubleshooting Back Office Panel](#troubleshooting-back-office-panel)
- [8. Credit Card Components](#8-credit-card-components)
  - [Enabling Components](#enabling-components)
  - [JavaScript Library Configuration](#javascript-library-configuration)
  - [Benefits](#benefits)
  - [Implementation Requirements](#implementation-requirements)
  - [Component Example](#component-example)
- [9. Wallet Payments](#9-wallet-payments)
  - [Wallet Configuration](#wallet-configuration)
  - [Apple Pay Integration](#apple-pay-integration)
  - [Setup Steps](#setup-steps)
- [10. Payment Links](#10-payment-links)
  - [Overview](#overview-1)
  - [API & Configuration Setup](#api--configuration-setup)
  - [Enabling Payment Links](#enabling-payment-links)
  - [Configuration Options](#configuration-options)
  - [Generating a Payment Link](#generating-a-payment-link)
  - [Backoffice: Creating & Managing Payment Links](#backoffice-creating--managing-payment-links)
  - [Creating a Link](#creating-a-link)
  - [Viewing Link Status](#viewing-link-status)
  - [Webhook Events for Payment Links](#webhook-events-for-payment-links)
  - [Link-Specific Status Mapping](#link-specific-status-mapping)
  - [Handling Expiry & Cancellation Events](#handling-expiry--cancellation-events)
  - [Testing Payment Links](#testing-payment-links)
- [11. Testing & Debugging](#11-testing--debugging)
  - [Test Mode Setup](#test-mode-setup)
  - [Test Credit Cards](#test-credit-cards)
  - [Debug Logging](#debug-logging)
  - [How to Enable Debug Mode](#how-to-enable-debug-mode)
  - [Log Levels and Content](#log-levels-and-content)
  - [Sensitive Data Masking](#sensitive-data-masking)
  - [Common Test Issues](#common-test-issues)
- [12. Production Deployment](#12-production-deployment)
  - [Pre-Production Checklist](#pre-production-checklist)
  - [Production Configuration](#production-configuration)
- [13. Troubleshooting](#13-troubleshooting)
  - [Payment methods not displaying at checkout](#payment-methods-not-displaying-at-checkout)
  - [Webhooks not being received](#webhooks-not-being-received)
  - [Credit card components not loading](#credit-card-components-not-loading)
  - [Apple Pay not appearing](#apple-pay-not-appearing)
- [14. Webhook Handling](#14-webhook-handling)
  - [How Webhooks Work](#how-webhooks-work)
  - [Webhook Configuration](#webhook-configuration)
  - [Webhook Payload Structure](#webhook-payload-structure)
  - [Payment Status Mapping](#payment-status-mapping)
  - [Webhook Retry Behavior](#webhook-retry-behavior)
- [15. Next-Gen Webhooks](#15-next-gen-webhooks)
  - [What Are Next-Gen Webhooks?](#what-are-next-gen-webhooks)
  - [Key Improvements Over Legacy Webhooks](#key-improvements-over-legacy-webhooks)
  - [Setup & Configuration](#setup--configuration-1)
  - [Prerequisites](#prerequisites-1)
  - [Register a Webhook](#register-a-webhook)
  - [Choose a Delivery Mode](#choose-a-delivery-mode)
  - [Payload Structure](#payload-structure)
  - [Payment Link Event Types](#payment-link-event-types)
  - [Test Your Webhook](#test-your-webhook)
  - [Manage Webhooks](#manage-webhooks)
  - [Security & Validation](#security--validation)
  - [Signature Verification](#signature-verification)
  - [Validation Checklist](#validation-checklist)
  - [Idempotency](#idempotency)
  - [Respond Fast, Process Async](#respond-fast-process-async)
  - [Security Best Practices](#security-best-practices)
- [16. Webhook Error Troubleshooting](#16-webhook-error-troubleshooting)
  - [Common Webhook Issues](#common-webhook-issues)
  - [Issue 1: Webhooks Not Being Received](#issue-1-webhooks-not-being-received)
  - [Issue 2: Webhooks Received But Not Processed](#issue-2-webhooks-received-but-not-processed)
  - [Getting Help with Webhook Issues](#getting-help-with-webhook-issues)
- [Getting Help](#getting-help)

## 1. Prerequisites

Before starting the integration, ensure you have the following:

- ✅ Active Mollie merchant account (Sign up here)
- ✅ Mollie API credentials (API key and Profile ID)
- ✅ Spryker Commerce Platform installed and configured
- ✅ Access to Spryker configuration files
- ✅ SSL certificate for production environment (required for webhooks)
- ✅ Basic authentication credentials for test environment

> **Important Security Note**
>
> Never commit API keys or credentials directly to version control. Always use environment variables or secure secret management systems.

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
		'molliePrzelewy24Payment' => 'przelewy24',  
		'mollieMultibancoPayment' => 'multibanco',  
		'mollieBancomatPayPayment' => 'bancomatpay',  
		'mollieBizumPayment' => 'bizum',  
		'mollieBilliePayment' => 'billie',  
		'mollieRivertyPayment' => 'riverty',  
		'mollieIdealIn3Payment' => 'in3',  
		'mollieAlmaPayment' => 'alma',  
		'mollieBLIKPayment' => 'blik',  
		'molliePayconiqPayment' => 'payconiq',  
		'mollieTrustlyPayment' => 'trustly',  
		'mollieSwishPayment' => 'swish',  
		'mollieMbWayPayment' => 'mbway',  
		'mollieSatispayPayment' => 'satispay',  
		'mollieTwintPayment' => 'twint',  
		'mollieVippsPayment' => 'vipps',
    ],
    MollieConstants::MOLLIE_INCLUDE_WALLETS => ['applepay'],
    MollieConstants::MOLLIE_PAYMENT_METHOD_MANUAL_CAPTURE => [  
	  'klarna',  
	  'vipps',  
	],
    MollieConstants::MOLLIE_PAYMENT_LINK_EXPIRATION_TIME => 10080,  
	MollieConstants::MOLLIE_NEXT_GEN_WEBHOOK_SIGNING_SECRET => getenv('MOLLIE_NEXT_GEN_WEBHOOK_SIGNING_SECRET') ?: '',  
	MollieConstants::MOLLIE_BNPL_PAYMENT_METHODS => ['billie', 'in3', 'klarna', 'riverty', 'voucher', 'alma'],
	MollieConstants::MOLLIE_GET_METHODS_API_DEFAULT_AMOUNT_VALUE => '100.00',
];
```

To enable Spryker kernel recognition for the Mollie module (Facades, Clients, Services, etc.), it is necessary to register Mollie as a core namespace in config_default.php:

```php
$config[KernelConstants::CORE_NAMESPACES] = [
    'SprykerShop',
    'SprykerEco',
    'Spryker',
    'SprykerSdk',
    'Mollie',
];
```

Without this, Spryker's dependency locator will be unable to resolve Mollie module automatically.

### TypeScript Component Recognition

To enable TypeScript component recognition for the Mollie module during the frontend build (npm run yves), the Mollie vendor path must be registered in frontend/settings.js.

Define the path under globalSettings.paths:

```js
// mollie folders
mollie: './vendor/mollie'
```

Expose it in the local paths object inside getAppSettingsByTheme:

```js
mollie: globalSettings.paths.mollie
```

Include it in the componentEntryPoints.dirs array so the frontend builder scans Mollie's components for TypeScript entry points alongside other core and eco modules:

```js
join(globalSettings.context, paths.mollie)
```

Without this, any index.ts components inside the Mollie module will not be picked up during the build.

### API Key Configuration

The Mollie API key is the primary authentication mechanism for all API requests.

**Configuration Parameter:**

```php
MollieConstants::MOLLIE_API_KEY => getenv('MOLLIE_API_KEY') ?: ''
```

**Setup Steps:**

1. Log in to your Mollie Dashboard
2. Navigate to **Developers → API Keys**
3. Copy your **Test API key** (starts with `test_`) or **Live API key** (starts with `live_`)
4. Add the key to your parameter store:

```bash
MOLLIE_API_KEY='test_YourApiKeyHere'
```

> **Security Best Practice**
>
> Store API keys as environment variables. The configuration uses `getenv('MOLLIE_API_KEY')` to retrieve the key securely without exposing it in your codebase.

### Profile ID Configuration

The Profile ID identifies your Mollie payment profile and is required for operations like fetching available payment methods.

**Configuration Parameter:**

```php
MollieConstants::MOLLIE_PROFILE_ID => 'profile_id_example'
```

**How to Find Your Profile ID:**

1. Log in to your Mollie Dashboard
2. Navigate to **Developers → API Keys**
3. The Profile ID is displayed at the bottom (format: `pfl_xxxxxxxxxx`)

> **Profile ID Format**
>
> Profile IDs always start with `pfl_` followed by 10 alphanumeric characters.
>
> Example: `pfl_pD5UernhAv`

### Environment Settings

#### Test Mode

```php
MollieConstants::MOLLIE_TEST_MODE => true
```

| Value | Description | Use Case |
|-------|-------------|----------|
| `true` | Use test mode with test API credentials | Development, staging, testing |
| `false` | Use production mode with live API credentials | Production environment |

**Test Mode Benefits:**

- No actual charges occur
- Use test payment methods and test cards
- Simulate different payment scenarios (success, failure, cancellation)
- Safe environment for integration testing

#### Debug Mode

```php
MollieConstants::MOLLIE_DEBUG_MODE => 'Extensive'
```

| Value | Log Level | What Gets Logged |
|-------|-----------|-----------------|
| `'Off'` | No logging | - |
| `'Basic'` | Essential information only | Request URL, status code, timestamps, high-level events |
| `'Extensive'` | Comprehensive | All API requests, responses, internal operations, state changes |

**Recommended Debug Settings:**

- **Development/Testing:** `'Extensive'` for full visibility
- **Staging:** `'Basic'` for error tracking
- **Production:** `'Basic'` or `'None'` for performance

### URL Configuration

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

##### Option A: With HTTP Basic Authentication (If Using .htaccess or Similar)

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

##### Option B: Without Basic Authentication (Publicly Accessible Test Environment)

If your test environment is publicly accessible without basic authentication:

```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

Example: `https://test.yourstore.com/mollie/webhook`

> **Configuration Guidance**
>
> **Use Option A when:**
> - Your staging/test environment uses .htaccess protection
> - You have HTTP basic authentication enabled via web server configuration
> - You want to restrict public access to test endpoints
>
> **Use Option B when:**
> - Your test environment is publicly accessible
> - You use alternative security methods (IP whitelisting, VPN, etc.)
> - Your test server has HTTPS configured
>
> **Security Note:** If using Option B without basic auth, consider implementing alternative security measures such as IP whitelisting for Mollie's webhook servers or restricting access via firewall rules.

### HTTPS vs HTTP for Test Environments

- **HTTPS (Recommended):** Use `https://` if your test environment has a valid SSL certificate
- **HTTP:** Only use `http://` for local development or internal networks not accessible from the internet

**HTTPS Configuration Example:**

```php
MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => sprintf(
    'https://%s%s',
    $sprykerFrontendHost,
    '/mollie/webhook'
)
```

## 3. Setting Up Mollie Payment Methods

After basic Mollie configuration is complete, you must:

- Add payment methods to CSV
- Assign them to stores
- Import them
- Configure OMS mapping
- Register handlers & subforms
- Add Twig form configuration

### 3.1. Add Mollie Payment Methods to CSV

**Step 1 — Copy Available Methods**

Open:

```
vendor/mollie/spryker-payment/data/import/common/common/payment_method.csv
```

Copy the payment methods you want to enable in your shop.

Example vendor files:

```
vendor/mollie/spryker-payment/data/import/common/common/payment_method.csv
vendor/mollie/spryker-payment/data/import/common/DE/payment_method_store.csv
```

**Step 2 — Paste Into Project-Level CSV**

Paste selected rows into:

```
data/import/common/common/payment_method.csv
```

**Example (all available Mollie methods)**

```csv
mollieCreditCardPayment,Credit Card,MollieCreditCardPayment,Mollie Credit Card Payment,1
molliePayPalPayment,PayPal,MolliePayPalPayment,Mollie PayPal Payment,1
mollieBankTransferPayment,Bank Transfer,MollieBankTransferPayment,Mollie Bank Transfer Payment,1
mollieKlarnaPayment,Klarna,MollieKlarnaPayment,Mollie Klarna Payment,1
mollieKlarnaPayLaterPayment,Klarna Pay Later,MollieKlarnaPayLaterPayment,Mollie Klarna Pay Later Payment,1
mollieKlarnaPayNowPayment,Klarna Pay Now,MollieKlarnaPayNowPayment,Mollie Klarna Pay Now Payment,1
mollieKlarnaSliceItPayment,Klarna Slice It,MollieKlarnaSliceItPayment,Mollie Slice It Payment,1
mollieEpsPayment,EPS,MollieEpsPayment,Mollie EPS Payment,1
mollieIdealPayment,iDEAL,MollieIdealPayment,Mollie iDEAL Payment,1
mollieBancontactPayment,Bancontact,MollieBancontactPayment,Mollie Bancontact Payment,1
mollieKbcPayment,KBC,MollieKbcPayment,Mollie KBC Payment,1
molliePayByBankPayment,Pay By Bank,MolliePayByBankPayment,Mollie Pay By Bank Payment,1
mollieApplePayPayment,Apple Pay,MollieApplePayPayment,Mollie Apple Pay Payment,1
mollieBilliePayment,Billie,MollieBilliePayment,Mollie Billie Payment,1  
mollieRivertyPayment,Riverty,MollieRivertyPayment,Mollie Riverty Payment,1  
mollieIdealIn3Payment,iDEAL in3,MollieIdealIn3Payment,Mollie iDEAL in3 Payment,1  
mollieAlmaPayment,Alma,MollieAlmaPayment,Mollie Alma Payment,1  
molliePrzelewy24Payment,Przelewy 24,MolliePrzelewy24Payment,Mollie Przelewy 24 Payment,1  
mollieBancomatPayPayment,Bancomat Pay,MollieBancomatPayPayment,Mollie Bancomat Pay Payment,1  
mollieMultibancoPayment,Multibanco,MollieMultibancoPayment,Mollie Multibanco Payment,1  
mollieBizumPayment,Bizum,MollieBizumPayment,Mollie Bizum Payment,1  
mollieBLIKPayment,BLIK,MollieBLIKPayment,Mollie BLIK Payment,1  
molliePayconiqPayment,Payconiq,MolliePayconiqPayment,Mollie Payconiq Payment,1  
mollieTrustlyPayment,Trustly,MollieTrustlyPayment,Mollie Trustly Payment,1  
mollieMbWayPayment,MB Way,MollieMbWayPayment,Mollie MB Way Payment,1  
mollieSwishPayment,Swish,MollieSwishPayment,Mollie Swish Payment,1  
mollieSatispayPayment,Satispay,MollieSatispayPayment,Mollie Satispay Payment,1  
mollieTwintPayment,TWINT,MollieTwintPayment,Mollie TWINT Payment,1  
mollieVippsPayment,Vipps,MollieVippsPayment,Mollie Vipps Payment,1
```

> **Important**
>
> Do NOT modify the payment method keys. They must match exactly.

### 3.2 Assign Payment Methods to Stores

Each store has its own CSV:

```
data/import/common/{STORE_NAME}/payment_method_store.csv
```

Example for `DE` store:

```csv
payment_method_key,store
mollieCreditCardPayment,DE
molliePayPalPayment,DE
mollieBankTransferPayment,DE
mollieKlarnaPayment,DE
mollieKlarnaPayLaterPayment,DE
mollieKlarnaPayNowPayment,DE
mollieKlarnaSliceItPayment,DE
mollieEpsPayment,DE
mollieIdealPayment,DE
mollieBancontactPayment,DE
mollieKbcPayment,DE
molliePayByBankPayment,DE
mollieApplePayPayment,DE
```

### 3.3 Import Payment Methods

Run:

```bash
console data:import:payment-method
console data:import:payment-method-store
```

If you use multi-store, repeat per store if needed.

### 3.4 Configure OMS → Mollie Payment Mapping

Open:

```
config/Shared/config_default.php
```

Add:

```php
$config[MollieConstants::MOLLIE] = [
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
        'molliePrzelewy24Payment' => 'przelewy24',
        'mollieMultibancoPayment' => 'multibanco',
        'mollieBancomatPayPayment' => 'bancomatpay',
        'mollieBizumPayment' => 'bizum',
        'mollieBilliePayment' => 'billie',
        'mollieRivertyPayment' => 'riverty',
        'mollieIdealIn3Payment' => 'in3',
        'mollieAlmaPayment' => 'alma',
        'mollieBLIKPayment' => 'blik',
        'molliePayconiqPayment' => 'payconiq',
        'mollieTrustlyPayment' => 'trustly',
        'mollieSwishPayment' => 'swish',
        'mollieMbWayPayment' => 'mbway',
        'mollieSatispayPayment' => 'satispay',
        'mollieTwintPayment' => 'twint',
        'mollieVippsPayment' => 'vipps',
    ],
];
```

Remove entries for payment methods you are not using.

In order for Mollie's state machines to be recognized, the following must be configured in config_default.php.

Add Mollie's state machines to the active processes configuration:

```php
$config[OmsConstants::ACTIVE_PROCESSES] = [
    'MolliePaymentStateMachine01',
    'MolliePaymentStateMachineManualCapture01',
];
```

Add Mollie's state machine process location so Spryker can locate and load the state machine XML definitions:

```php
$config[OmsConstants::PROCESS_LOCATION] = [
    OmsConfig::DEFAULT_PROCESS_LOCATION,
    APPLICATION_VENDOR_DIR . '/mollie/spryker-payment/config/Zed/Oms',
];
```

Without these, Spryker's OMS will be unable to recognize and process Mollie's state machines.

Add payment method state machine mapping:

```php
$config[SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING] = [
    MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD => 'MolliePaymentStateMachineManualCapture01',
    MollieConfig::MOLLIE_PAYMENT_PAYPAL => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_BANK_TRANSFER => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_KLARNA => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_KLARNA_PAY_NOW => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_KLARNA_PAY_LATER => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_KLARNA_SLICE_IT => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_EPS => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_IDEAL => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_BANCONTACT => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_KBC => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_PAY_BY_BANK => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_APPLE_PAY => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_PRZELEWY24 => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_MULTIBANCO => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_BANCOMAT_PAY => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_BIZUM => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_BILLIE => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_RIVERTY => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_IDEAL_IN3 => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_ALMA => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_BLIK => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_PAYCONIQ => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_TRUSTLY => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_VIPPS => 'MolliePaymentStateMachineManualCapture01',  
	MollieConfig::MOLLIE_PAYMENT_SWISH => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_MB_WAY => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_TWINT => 'MolliePaymentStateMachine01',  
	MollieConfig::MOLLIE_PAYMENT_SATISPAY => 'MolliePaymentStateMachine01',
];
```

By default, all payment methods use automatic capturing.

To configure certain payment methods to use manual capture, replace state machine: `MolliePaymentStateMachine01` with `MolliePaymentStateMachineManualCapture01`

Not all payment methods support manual capturing. See docs for details: https://docs.mollie.com/docs/place-a-hold-for-a-payment

To configure which payment method should use manual capturing, add configuration as follows:

```php
$config[MollieConstants::MOLLIE] = [
  MollieConstants::MOLLIE_PAYMENT_METHOD_MANUAL_CAPTURE => [  
	  'klarna',  
	  'vipps',  
	],
]
```

Values passed in this collection should match with right hand side values defined in `MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING`.

### 3.5 Add Payment Forms to Twig

Open:

```
src/Pyz/Yves/CheckoutPage/Theme/default/views/payment/payment.twig
```

Add Mollie forms to `customForms`:

```twig
{% define data = {
    stepNumber: 3,
    customForms: {
        'MollieCreditCardPayment/creditCard': ['mollie-credit-card', 'Mollie'],
        'MolliePayPalPayment/paypal': ['mollie-paypal', 'Mollie'],
        'MollieBankTransferPayment/bankTransfer': ['mollie-bank-transfer', 'Mollie'],
        'MollieKlarnaPayment/klarna': ['mollie-klarna', 'Mollie'],
        'MollieKlarnaPayLaterPayment/klarnaPayLater': ['mollie-klarna-pay-later', 'Mollie'],
        'MollieKlarnaPayNowPayment/klarnaPayNow': ['mollie-klarna-pay-now', 'Mollie'],
        'MollieKlarnaSliceItPayment/klarnaSliceIt': ['mollie-klarna-slice-it', 'Mollie'],
        'MollieEpsPayment/eps': ['mollie-eps', 'Mollie'],
        'MollieIdealPayment/ideal': ['mollie-ideal', 'Mollie'],
        'MollieBancontactPayment/bancontact': ['mollie-bancontact', 'Mollie'],
        'MollieKbcPayment/kbc': ['mollie-kbc', 'Mollie'],
        'MolliePayByBankPayment/payByBank': ['mollie-pay-by-bank', 'Mollie'],
        'MollieApplePayPayment/applePay': ['mollie-apple-pay', 'Mollie'],
        'MolliePrzelewy24Payment/przelewy24': ['mollie-przelewy24', 'Mollie'],  
		'MollieBancomatPayPayment/bancomatPay': ['mollie-bancomat-pay', 'Mollie'],  
		'MollieMultibancoPayment/multibanco': ['mollie-multibanco', 'Mollie'],  
		'MollieBizumPayment/bizum': ['mollie-bizum', 'Mollie'],  
		'MollieBilliePayment/billie': ['mollie-billie', 'Mollie'],  
		'MollieRivertyPayment/riverty': ['mollie-riverty', 'Mollie'],  
		'MollieIdealIn3Payment/in3': ['mollie-ideal-in3', 'Mollie'],  
		'MollieAlmaPayment/alma': ['mollie-alma', 'Mollie'],  
		'MollieBLIKPayment/bizum': ['mollie-blik', 'Mollie'],  
		'MolliePayconiqPayment/bizum': ['mollie-payconiq', 'Mollie'],  
		'MollieTrustlyPayment/trustly': ['mollie-trustly', 'Mollie'],  
		'MollieMbWayPayment/mbway': ['mollie-mb-way', 'Mollie'],  
		'MollieSwishPayment/swish': ['mollie-swish', 'Mollie'],  
		'MollieSatispayPayment/satispay': ['mollie-satispay', 'Mollie'],  
		'MollieTwintPayment/twint': ['mollie-twint', 'Mollie'],  
		'MollieVippsPayment/vipps': ['mollie-vipps', 'Mollie'],
    },
} %}
```

### 3.6 Final Steps

After everything:

```bash
console transfer:generate
console data:import:payment-method
console data:import:payment-method-store
```

Clear cache if needed.

### 3.7 Displaying Payment Method Logos in Checkout

To display Mollie payment method logos on the checkout payment step, you need to extend the `contentItem` block in:

```
src/Pyz/Yves/CheckoutPage/Theme/default/views/payment/payment.twig
```

**Step 1 — Extend `contentItem` Block**

Add the following logic inside the `contentItem` block:

```twig
{% block contentItem %}
    {% set paymentName = embed.form.paymentSelection[key].vars.name %}
    {% set formVars = embed.form[paymentName].vars %}
    {% if formVars.logoUrl is defined 
      and formVars.isLogoVisible is defined 
      and formVars.isLogoVisible is true %}
      
      {% set logoUrl = embed.form[paymentName].vars.logoUrl %}
      {% include molecule('mollie-logo-image', 'Mollie') with {
          data: {
            logoUrl: logoUrl,
            paymentName: paymentName
          }} only %}
    {% endif %}

    {# rest of the code ... #}

{% endblock %}
```

This molecule:

- Displays the official payment method logo
- Ensures correct styling
- Keeps checkout UI consistent

### 3.8 Payment Method Overrides (Names, Logos, Minimum and Maximum amounts)

To customize payment method settings such as name, logo, and minimum or maximum transaction amounts, navigate to: **Administration → Mollie Payment Methods** in the Backoffice.
Locate the desired payment method and click the **Edit** button on the right-hand side.

---

#### Override Payment Method Name
To override the payment method name, update the corresponding glossary key. This can be done either via the Back Office or by modifying the glossary.csv file.

##### Via Backoffice:
1. Navigate to **Administration → Glossary**
2. Search for the key in the format: `mollie<PaymentMethodName>Payment` (e.g. molliePaypalPayment)
3. Click **Edit**, update the value to the desired name, and save the changes

##### Via glossary.csv:
1. Update the relevant glossary key `mollie<PaymentMethodName>Payment` (e.g. molliePaypalPayment)
2. Deploy the changes
3. Run the following command after deployment: `console data:import:glossary`
---
#### Override Payment Method Logo
To change the payment method logo, provide a new image URL in the designated field and click **Save**.
The updated logo will be displayed on the shop’s payment page.

---

#### Override Payment Method Min/Max Amounts
To define minimum and maximum transaction limits enter the desired values in the corresponding input fields and click Save to apply the changes.


## 4. Dependency Provider Configuration

To ensure all Mollie functionalities work correctly, you must inject the required dependencies into the appropriate dependency providers. This section lists all dependency providers and their corresponding plugin injections.

> **Important**
>
> All dependency provider modifications must be completed before configuring the Mollie payment settings. Missing dependencies will cause payment methods to fail.

### 4.1. Router Dependency Provider

#### Route Provider Plugin

**Purpose:** Registers Mollie routes for handling payment redirects and webhooks.

**File:** `src/Pyz/Yves/Router/RouterDependencyProvider.php`

**Method:** `getRouteProvider`

**Plugin:** `vendor/mollie/spryker-payment/src/Mollie\Yves\Mollie\Plugin\Router\MollieRouteProviderPlugin`

```php
<?php

namespace Pyz\Yves\Router;

use Mollie\Yves\Mollie\Plugin\Router\MollieRouteProviderPlugin;
use Spryker\Yves\Router\RouterDependencyProvider as SprykerRouterDependencyProvider;

class RouterDependencyProvider extends SprykerRouterDependencyProvider
{
    // ... rest of the implementation

    /**
     * @return array<\Spryker\Yves\RouterExtension\Dependency\Plugin\RouteProviderPluginInterface>
     */
    protected function getRouteProvider(): array
    {
        return [
            // ... other route providers
            new MollieRouteProviderPlugin(),
        ];
    }
}
```

### 4.2. Checkout Dependency Provider (Zed)

#### Post Hooks

**Purpose:** Handles post-checkout operations for Mollie payments.

**File:** `src/Pyz/Zed/Checkout/CheckoutDependencyProvider.php`

**Method:** `getCheckoutPostHooks`

**Plugin:** `vendor/mollie/spryker-payment/src/Mollie\Zed\Mollie\Communication\Plugin\Checkout\MollieCheckoutPostSavePlugin`

```php
<?php

namespace Pyz\Zed\Checkout;

use Mollie\Zed\Mollie\Communication\Plugin\Checkout\MollieCheckoutPostSavePlugin;
use Spryker\Zed\Checkout\CheckoutDependencyProvider as SprykerCheckoutDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CheckoutDependencyProvider extends SprykerCheckoutDependencyProvider
{
    // ... rest of the implementation

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return array<\Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPostSaveInterface>
     */
    protected function getCheckoutPostHooks(Container $container): array
    {
        return [
            // ... other post hooks
            new MollieCheckoutPostSavePlugin(),
        ];
    }
}
```

### 4.3. Checkout Page Dependency Provider (Yves)

#### Payment Method Handlers

**Purpose:** Registers payment method handlers and sub-forms for the checkout process.

**File:** `src/Pyz/Yves/CheckoutPage/CheckoutPageDependencyProvider.php`

**Method:** `extendPaymentMethodHandler`

**Plugins:** All Mollie payment method handler plugins

```php
<?php

namespace Pyz\Yves\CheckoutPage;

use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieAlmaPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieApplePayPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieBancomatPayPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieBancontactPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieBankTransferPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieBilliePaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieBizumPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieBLIKPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieCreditCardPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieEpsPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieIdealIn3PaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieIdealPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieKbcPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieKlarnaPayLaterPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieKlarnaPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieKlarnaPayNowPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieKlarnaSliceItPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieMbWayPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieMultibancoPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MolliePayByBankPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MolliePayconiqPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MolliePayPalPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MolliePrzelewy24PaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieRivertyPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieSatispayPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieSwishPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieTrustlyPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieTwintPaymentHandlerPlugin;  
use Mollie\Yves\Mollie\Plugin\PaymentHandler\MollieVippsPaymentHandlerPlugin;


class CheckoutPageDependencyProvider extends SprykerShopCheckoutPageDependencyProvider
{

  // ... rest of the implementation

 protected function extendPaymentMethodHandler(Container $container): Container
    {
        $container->extend(static::PAYMENT_METHOD_HANDLER, function (StepHandlerPluginCollection $paymentMethodHandler) {
            // ... other payment method handlers
            $paymentMethodHandler->add(new PaymentForeignHandlerPlugin(), PaymentTransfer::FOREIGN_PAYMENTS);
            $paymentMethodHandler->add(new MollieCreditCardPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD);
            $paymentMethodHandler->add(new MolliePayPalPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_PAYPAL);
            $paymentMethodHandler->add(new MollieBankTransferPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_BANK_TRANSFER);
            $paymentMethodHandler->add(new MollieKlarnaPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_KLARNA);
            $paymentMethodHandler->add(new MollieKlarnaPayLaterPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_KLARNA_PAY_LATER);
            $paymentMethodHandler->add(new MollieKlarnaPayNowPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_KLARNA_PAY_NOW);
            $paymentMethodHandler->add(new MollieKlarnaSliceItPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_KLARNA_SLICE_IT);
            $paymentMethodHandler->add(new MollieEpsPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_EPS);
            $paymentMethodHandler->add(new MollieIdealPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_IDEAL);
            $paymentMethodHandler->add(new MollieBancontactPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_BANCONTACT);
            $paymentMethodHandler->add(new MollieKbcPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_KBC);
            $paymentMethodHandler->add(new MolliePayByBankPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_PAY_BY_BANK);
            $paymentMethodHandler->add(new MollieApplePayPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_APPLE_PAY);
			$paymentMethodHandler->add(new MolliePrzelewy24PaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_PRZELEWY24);  
			$paymentMethodHandler->add(new MollieBancomatPayPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_BANCOMAT_PAY);  
			$paymentMethodHandler->add(new MollieMultibancoPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_MULTIBANCO);  
			$paymentMethodHandler->add(new MollieBizumPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_BIZUM);  
			$paymentMethodHandler->add(new MollieBilliePaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_BILLIE);  
			$paymentMethodHandler->add(new MollieRivertyPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_RIVERTY);  
			$paymentMethodHandler->add(new MollieIdealIn3PaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_IDEAL_IN3);  
			$paymentMethodHandler->add(new MollieAlmaPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_ALMA);  
			$paymentMethodHandler->add(new MollieBLIKPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_BLIK);  
			$paymentMethodHandler->add(new MolliePayconiqPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_PAYCONIQ);  
			$paymentMethodHandler->add(new MollieTrustlyPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_TRUSTLY);  
			$paymentMethodHandler->add(new MollieVippsPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_VIPPS);  
			$paymentMethodHandler->add(new MollieSatispayPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_SATISPAY);  
			$paymentMethodHandler->add(new MollieTwintPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_TWINT);  
			$paymentMethodHandler->add(new MollieSwishPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_SWISH);  
			$paymentMethodHandler->add(new MollieMbWayPaymentHandlerPlugin(), MollieConfig::MOLLIE_PAYMENT_MB_WAY);
			
            return $paymentMethodHandler;
        });

        return $container;
    }
}
```

#### Subform Plugin Collection

**Method:** `extendSubFormPluginCollection`

**Plugins:** All Mollie payment sub-form plugins

```php
<?php

namespace Pyz\Yves\CheckoutPage;

use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieAlmaSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieApplePaySubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieBancomatPaySubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieBancontactSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieBankTransferSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieBillieSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieBizumSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieBLIKSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieCreditCardSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieEpsSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieIdealIn3SubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieIdealSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieKbcSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieKlarnaPayLaterSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieKlarnaPayNowSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieKlarnaSliceItSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieKlarnaSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieMbWaySubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieMultibancoSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MolliePayByBankSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MolliePayconiqSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MolliePayPalSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MolliePrzelewy24SubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieRivertySubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieSatispaySubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieSwishSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieTrustlySubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieTwintSubFormPlugin;  
use Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin\MollieVippsSubFormPlugin;
use SprykerShop\Yves\CheckoutPage\CheckoutPageDependencyProvider as SprykerShopCheckoutPageDependencyProvider;
use Spryker\Yves\Kernel\Container;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;

class CheckoutPageDependencyProvider extends SprykerShopCheckoutPageDependencyProvider
{
    // ... rest of the implementation

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function extendSubFormPluginCollection(Container $container): Container
    {
        $container->extend(static::PAYMENT_SUB_FORMS, function (SubFormPluginCollection $paymentSubFormPluginCollection) {
            // ... other sub-forms

            // Mollie Payment Sub-Forms
            $paymentSubFormPluginCollection->add(new MollieCreditCardSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MolliePayPalSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieBankTransferSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieKlarnaSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieKlarnaPayLaterSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieKlarnaPayNowSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieKlarnaSliceItSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieEpsSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieIdealSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieBancontactSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieKbcSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MolliePayByBankSubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieApplePaySubFormPlugin());
            $paymentSubFormPluginCollection->add(new MollieBancomatPaySubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieBizumSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieMultibancoSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MolliePrzelewy24SubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieBLIKSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MolliePayconiqSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieBillieSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieRivertySubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieIdealIn3SubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieAlmaSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieTrustlySubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieMbWaySubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieSwishSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieSatispaySubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieTwintSubFormPlugin());  
			$paymentSubFormPluginCollection->add(new MollieVippsSubFormPlugin());

            return $paymentSubFormPluginCollection;
        });

        return $container;
    }
}
```

#### Checkout Summary Step Pre-Condition Plugins

**Method:** `getCheckoutSummaryStepPreConditionPlugins`

**Plugin:** `SprykerShop\Yves\PaymentAppWidget\Plugin\CheckoutPage\PaymentAppCancelOrderOnSummaryPageAfterRedirectFromHostedPaymentPagePlugin`

```php
<?php

namespace Pyz\Yves\CheckoutPage;

use SprykerShop\Yves\CheckoutPage\CheckoutPageDependencyProvider as SprykerShopCheckoutPageDependencyProvider;
use SprykerShop\Yves\PaymentAppWidget\Plugin\CheckoutPage\PaymentAppCancelOrderOnSummaryPageAfterRedirectFromHostedPaymentPagePlugin;

class CheckoutPageDependencyProvider extends SprykerShopCheckoutPageDependencyProvider
{
    // ... rest of the implementation

    /**
     * @return array<\SprykerShop\Yves\CheckoutPageExtension\Dependency\Plugin\CheckoutStepResolverStrategyPluginInterface>
     */
    protected function getCheckoutSummaryStepPreConditionPlugins(): array
    {
        return [
            new PaymentAppCancelOrderOnSummaryPageAfterRedirectFromHostedPaymentPagePlugin(),
        ];
    }
}
```

For **PaymentAppCancelOrderOnSummaryPageAfterRedirectFromHostedPaymentPagePlugin** to work correctly, you must set **is_foreign** key to **1** in table **spy_payment_method** on all Mollie payment methods

<img src="../.github/images/spy-payment-method-table.png" alt="Spy Payment Method Table" width="600" />

### 4.4. Payment Dependency Provider

#### Payment Method Filter Plugins

**Purpose:** Filters available payment methods based on Mollie configuration.

**File:** `src/Pyz/Zed/Payment/PaymentDependencyProvider.php`

**Method:** `getPaymentMethodFilterPlugins`

**Plugin:** `Mollie\Zed\Mollie\Communication\Plugin\Payment\ActiveMolliePaymentMethodFilterPlugin`

```php
<?php

namespace Pyz\Zed\Payment;

use Mollie\Zed\Mollie\Communication\Plugin\Payment\ActiveMolliePaymentMethodFilterPlugin;
use Spryker\Zed\Payment\PaymentDependencyProvider as SprykerPaymentDependencyProvider;

class PaymentDependencyProvider extends SprykerPaymentDependencyProvider
{
    // ... rest of the implementation

    /**
     * @return array<\Spryker\Zed\PaymentExtension\Dependency\Plugin\PaymentMethodFilterPluginInterface>
     */
    protected function getPaymentMethodFilterPlugins(): array
    {
        return [
            // ... other payment filters
            new ActiveMolliePaymentMethodFilterPlugin(),
        ];
    }
}
```

### 4.5. OMS Dependency Provider

#### Command Plugins

**Purpose:** Registers OMS commands and conditions for Mollie payment state machine.

**File:** `src/Pyz/Zed/Oms/OmsDependencyProvider.php`

```php
<?php

namespace Pyz\Zed\Oms;

use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MolliePaymentCaptureCommandPlugin;  
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MolliePaymentConfirmationCommandPlugin;  
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MolliePaymentLinkCreateCommandPlugin;  
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MolliePaymentLinkSetExpireCommandPlugin;  
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MollieRefundCommandPlugin;  
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MollieReleaseAuthorizationCommandPlugin;

use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandCollectionInterface;
use Spryker\Zed\Oms\OmsDependencyProvider as SprykerOmsDependencyProvider;

class OmsDependencyProvider extends SprykerOmsDependencyProvider
{
    // ... rest of the implementation

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function extendCommandPlugins(Container $container): Container
    {
        $container->extend(self::COMMAND_PLUGINS, function (CommandCollectionInterface $commandCollection) {
            // ... other commands

            // Mollie OMS Commands
            $commandCollection->add(new MollieRefundCommandPlugin(), 'Mollie/Refund');
            $commandCollection->add(new MolliePaymentConfirmationCommandPlugin(), 'Mollie/PaymentConfirmation');

            // If using manual capture:
            $commandCollection->add(new MolliePaymentCaptureCommandPlugin(), 'Mollie/MolliePaymentCapture');

			$commandCollection->add(new MollieReleaseAuthorizationCommandPlugin(), 'Mollie/MollieReleaseAuthorization');  

			// If using payment links:
			$commandCollection->add(new MolliePaymentConfirmationCommandPlugin(), 'Mollie/PaymentConfirmation');  
			$commandCollection->add(new MolliePaymentLinkCreateCommandPlugin(), 'Mollie/CreatePaymentLink');  
			$commandCollection->add(new MolliePaymentLinkSetExpireCommandPlugin(), 'Mollie/SetExpireStatusPaymentLink');

            return $commandCollection;
        });

        return $container;
    }
}
```

#### Condition Plugins

**Method:** `extendConditionPlugins`

**Plugins:** All Mollie payment status condition plugins

```php
<?php

namespace Pyz\Zed\Oms;

use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\MollieIsPaymentStatusCancelledConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\MollieIsPaymentStatusExpiredConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\MollieIsPaymentStatusFailedConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\MollieIsPaymentStatusPaidConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\MollieIsRefundStatusFailedConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\MollieIsRefundStatusRefundedConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\Authorization\IsAuthorizationCanceledConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\Authorization\IsAuthorizationExpiredConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\Authorization\IsAuthorizationFailedConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\Authorization\IsAuthorizedConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\Capture\IsCapturedConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\Capture\IsCaptureFailedConditionPlugin;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\PaymentLink\MollieIsPaymentLinkCreationSuccessfulConditionPlugin;  
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\PaymentLink\MollieIsPaymentLinkStatusExpiredConditionPlugin;  
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\PaymentLink\MollieIsPaymentLinkStatusPaidConditionPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionCollectionInterface;
use Spryker\Zed\Oms\OmsDependencyProvider as SprykerOmsDependencyProvider;

class OmsDependencyProvider extends SprykerOmsDependencyProvider
{
    // ... rest of the implementation

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function extendConditionPlugins(Container $container): Container
    {
        $container->extend(self::CONDITION_PLUGINS, function (ConditionCollectionInterface $conditionCollection) {
            // ... other conditions

            // Mollie OMS Conditions
            $conditionCollection->add(new MollieIsPaymentStatusPaidConditionPlugin(), 'Mollie/IsPaymentStatusPaid');
            $conditionCollection->add(new MollieIsPaymentStatusExpiredConditionPlugin(), 'Mollie/IsPaymentStatusExpired');
            $conditionCollection->add(new MollieIsPaymentStatusCancelledConditionPlugin(), 'Mollie/IsPaymentStatusCancelled');
            $conditionCollection->add(new MollieIsPaymentStatusFailedConditionPlugin(), 'Mollie/IsPaymentStatusFailed');
            $conditionCollection->add(new MollieIsRefundStatusRefundedConditionPlugin(), 'Mollie/IsRefundStatusRefunded');
            $conditionCollection->add(new MollieIsRefundStatusFailedConditionPlugin(), 'Mollie/IsRefundStatusFailed');

            // If using manual capture:
            $conditionCollection->add(new IsAuthorizedConditionPlugin(), 'Mollie/IsAuthorized');
            $conditionCollection->add(new IsAuthorizationCanceledConditionPlugin(), 'Mollie/IsAuthorizationCanceled');
            $conditionCollection->add(new IsAuthorizationExpiredConditionPlugin(), 'Mollie/IsAuthorizationExpired');
            $conditionCollection->add(new IsAuthorizationFailedConditionPlugin(), 'Mollie/IsAuthorizationFailed');
            $conditionCollection->add(new IsCapturedConditionPlugin(), 'Mollie/IsCaptured');
            $conditionCollection->add(new IsCaptureFailedConditionPlugin(), 'Mollie/IsCaptureFailed');

			// If using payment links:
			$conditionCollection->add(new MollieIsPaymentLinkCreationSuccessfulConditionPlugin(), 'Mollie/IsPaymentLinkCreationSuccessful');  
			$conditionCollection->add(new MollieIsPaymentLinkStatusPaidConditionPlugin(), 'Mollie/IsPaymentLinkStatusPaid');  
			$conditionCollection->add(new MollieIsPaymentLinkStatusExpiredConditionPlugin(), 'Mollie/IsPaymentLinkStatusExpired');

            return $conditionCollection;
        });

        return $container;
    }
}
```

### 4.6. Mail Dependency Provider

#### Command Plugins

**Purpose:** Registers mail type builder for payment confirmation emails.

**File:** `src/Pyz/Zed/Mail/MailDependencyProvider.php`

**Method:** `getMailTypeBuilderPlugins`

**Plugin:** `Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MolliePaymentConfirmationMailTypeBuilderPlugin`

```php
<?php

namespace Pyz\Zed\Mail;

use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MolliePaymentConfirmationMailTypeBuilderPlugin;
use Spryker\Zed\Mail\MailDependencyProvider as SprykerMailDependencyProvider;

class MailDependencyProvider extends SprykerMailDependencyProvider
{
    // ... rest of the implementation

    /**
     * @return array<\Spryker\Zed\MailExtension\Dependency\Plugin\MailTypeBuilderPluginInterface>
     */
    protected function getMailTypeBuilderPlugins(): array
    {
        return [
            // ... other mail types
            new MolliePaymentConfirmationMailTypeBuilderPlugin(),
        ];
    }
}
```

## 5. Glossary Keys and Translations

The Mollie module includes comprehensive translation files for all payment methods, checkout labels, OMS states, and email templates. The translations are available in English (en_US) and German (de_DE).

**Key Categories:**

- Payment Method Names - Display names for each payment method
- Checkout Payment Provider Labels - Labels shown in checkout
- Credit Card Form Labels - Form field labels for credit card input
- Payment Method Descriptions - Descriptive text for each payment method
- OMS State Labels - Order management system state translations
- Email Confirmation Messages - Payment confirmation email content

### Complete Glossary CSV

To import all Mollie translations, update CSV file with the following content:

**File location:** `data/import/common/common/glossary.csv`

```csv
mollieCreditCardPayment,Kreditkarte,de_DE
mollieCreditCardPayment,Credit card,en_US
molliePayPalPayment,PayPal,de_DE
molliePayPalPayment,PayPal,en_US
mollieBankTransferPayment,Banküberweisung,de_DE
mollieBankTransferPayment,Bank transfer,en_US
mollieKlarnaPayment,Klarna,de_DE
mollieKlarnaPayment,Klarna,en_US
mollieKlarnaPayLaterPayment,Klarna Pay Later,de_DE
mollieKlarnaPayLaterPayment,Klarna Pay Later,en_US
mollieKlarnaPayNowPayment,Klarna Pay Now,de_DE
mollieKlarnaPayNowPayment,Klarna Pay Now,en_US
mollieKlarnaSliceItPayment,Klarna Slice It,de_DE
mollieKlarnaSliceItPayment,Klarna Slice It,en_US
mollieEpsPayment,EPS,de_DE
mollieEpsPayment,EPS,en_US
mollieIdealPayment,iDEAL,de_DE
mollieIdealPayment,iDEAL,en_US
mollieBancontactPayment,Bancontact,de_DE
mollieBancontactPayment,Bancontact,en_US
mollieKbcPayment,KBC,de_DE
mollieKbcPayment,KBC,en_US
molliePayByBankPayment,Per Banküberweisung bezahlen,de_DE
molliePayByBankPayment,Pay by bank transfer,en_US
mollieApplePayPayment,Apple Pay,de_DE
mollieApplePayPayment,Apple Pay,en_US
mollieBancomatPayPayment,Bancomat Pay,de_DE  
mollieBancomatPayPayment,Bancomat Pay,en_US  
mollieMultibancoPayment,Multibanco,de_DE  
mollieMultibancoPayment,Multibanco,en_US  
molliePrzelewy24Payment,Przelewy24,de_DE  
molliePrzelewy24Payment,Przelewy24,en_US  
mollieBizumPayment,Bizum,de_DE  
mollieBizumPayment,Bizum,en_US  
mollieBilliePayment,Billie,de_DE  
mollieBilliePayment,Billie,en_US  
mollieRivertyPayment,Riverty,de_DE  
mollieRivertyPayment,Riverty,en_US  
mollieIdealIn3Payment,iDEAL in3,de_DE  
mollieIdealIn3Payment,iDEAL in3,en_US  
mollieAlmaPayment,Alma,de_DE  
mollieAlmaPayment,Alma,en_US  
mollieTrustlyPayment,Trustly,de_DE  
mollieTrustlyPayment,Trustly,en_US  
mollieMbWayPayment,MB Way,en_US  
mollieMbWayPayment,MB Way,de_DE  
mollieSwishPayment,Swish,en_US  
mollieSwishPayment,Swish,de_DE  
mollieSatispayPayment,Satispay,de_DE  
mollieSatispayPayment,Satispay,en_US  
mollieTwintPayment,TWINT,en_US  
mollieTwintPayment,TWINT,de_DE  
mollieVippsPayment,Vipps,en_US  
mollieVippsPayment,Vipps,de_DE
mollie.checkout.payment.credit.card.missing.token,Der Zahlungstoken fehlt. Bitte füllen Sie das Zahlungsformular aus.,de_DE
mollie.checkout.payment.credit.card.missing.token,Payment token is missing. Please complete the payment form.,en_US
mollie.checkout.payment.credit.card.holder,Karteninhaber,de_DE
mollie.checkout.payment.credit.card.holder,Card holder,en_US
mollie.checkout.payment.credit.card.number,Kartennummer,de_DE
mollie.checkout.payment.credit.card.number,Card number,en_US
mollie.checkout.payment.credit.card.expiry.date,Ablaufdatum,de_DE
mollie.checkout.payment.credit.card.expiry.date,Card expiry,en_US
mollie.checkout.payment.credit.card.verification.code,Sicherheitscode,de_DE
mollie.checkout.payment.credit.card.verification.code,Verification code,en_US
checkout.payment.provider.MollieCreditCardPayment,Mollie Kreditkarte,de_DE
checkout.payment.provider.MollieCreditCardPayment,Mollie Credit Card,en_US
checkout.payment.provider.MolliePayPalPayment,Mollie PayPal,de_DE
checkout.payment.provider.MolliePayPalPayment,Mollie PayPal,en_US
checkout.payment.provider.MollieBankTransferPayment,Mollie Banküberweisung,de_DE
checkout.payment.provider.MollieBankTransferPayment,Mollie Bank Transfer,en_US
checkout.payment.provider.mollieKlarnaPayment,Mollie Klarna,de_DE
checkout.payment.provider.mollieKlarnaPayment,Mollie Klarna,en_US
checkout.payment.provider.MollieKlarnaPayLaterPayment,Mollie Klarna Pay Later,de_DE
checkout.payment.provider.MollieKlarnaPayLaterPayment,Mollie Klarna Pay Later,en_US
checkout.payment.provider.MollieKlarnaPayNowPayment,Mollie Klarna Pay Now,de_DE
checkout.payment.provider.MollieKlarnaPayNowPayment,Mollie Klarna Pay Now,en_US
checkout.payment.provider.MollieKlarnaSliceItPayment,Mollie Klarna Slice It,de_DE
checkout.payment.provider.MollieKlarnaSliceItPayment,Mollie Klarna Slice It,en_US
checkout.payment.provider.MollieEpsPayment,Mollie EPS,de_DE
checkout.payment.provider.MollieEpsPayment,Mollie EPS,en_US
checkout.payment.provider.MollieIdealPayment,Mollie iDEAL,de_DE
checkout.payment.provider.MollieIdealPayment,Mollie iDEAL,en_US
checkout.payment.provider.MollieBancontactPayment,Mollie Bancontact,de_DE
checkout.payment.provider.MollieBancontactPayment,Mollie Bancontact,en_US
checkout.payment.provider.MollieKbcPayment,Mollie KBC,de_DE
checkout.payment.provider.MollieKbcPayment,Mollie KBC,en_US
checkout.payment.provider.MolliePayByBankPayment,Mollie per Bank bezahlen,de_DE
checkout.payment.provider.MolliePayByBankPayment,Mollie Pay By Bank,en_US
checkout.payment.provider.MollieApplePayPayment,Mollie Apple Pay,de_DE
checkout.payment.provider.MollieApplePayPayment,Mollie Apple Pay,en_US
checkout.payment.provider.MollieBancomatPayPayment,Mollie Bancomat Pay,de_DE  
checkout.payment.provider.MollieBancomatPayPayment,Mollie Bancomat Pay,en_US  
checkout.payment.provider.MollieMultibancoPayment,Mollie Multibanco,de_DE  
checkout.payment.provider.MollieMultibancoPayment,Mollie Multibanco,en_US  
checkout.payment.provider.MolliePrzelewy24Payment,Mollie Przelewy24,de_DE  
checkout.payment.provider.MolliePrzelewy24Payment,Mollie Przelewy24,en_US  
checkout.payment.provider.MollieBizumPayment,Mollie Bizum,de_DE  
checkout.payment.provider.MollieBizumPayment,Mollie Bizum,en_US  
checkout.payment.provider.MollieBilliePayment,Mollie Billie,de_DE  
checkout.payment.provider.MollieBilliePayment,Mollie Billie,en_US  
checkout.payment.provider.MollieIdealIn3Payment,Mollie iDEAL in3,de_DE  
checkout.payment.provider.MollieIdealIn3Payment,Mollie iDEAL in3,en_US  
checkout.payment.provider.MollieRivertyPayment,Mollie Riverty,de_DE  
checkout.payment.provider.MollieRivertyPayment,Mollie Riverty,en_US  
checkout.payment.provider.MollieAlmaPayment,Mollie Alma,de_DE  
checkout.payment.provider.MollieAlmaPayment,Mollie Alma,en_US  
checkout.payment.provider.MollieMbWayPayment,Mollie MB Way,en_US  
checkout.payment.provider.MollieMbWayPayment,Mollie MB Way,de_DE  
checkout.payment.provider.MollieSatispayPayment,Mollie Satispay,en_US  
checkout.payment.provider.MollieSatispayPayment,Mollie Satispay,de_DE  
checkout.payment.provider.MollieVippsPayment,Mollie Vipps payment,en_US  
checkout.payment.provider.MollieVippsPayment,Mollie Vipps payment,de_DE  
checkout.payment.provider.MollieSwishPayment,Mollie Swish,de_DE  
checkout.payment.provider.MollieSwishPayment,Mollie Swish,en_US  
checkout.payment.provider.MollieTrustlyPayment,Mollie Trustly,en_US  
checkout.payment.provider.MollieTrustlyPayment,Mollie Trustly,de_DE  
checkout.payment.provider.MollieTwintPayment,Mollie Twint,en_US  
checkout.payment.provider.MollieTwintPayment,Mollie Twint,de_DE
checkout.payment.provider.mollieCreditCard.descriptionText,Schließen Sie Ihren Einkauf sicher mit Ihren Kreditkartendaten ab.,de_DE
checkout.payment.provider.mollieCreditCard.descriptionText,Complete your purchase securely using your credit card credentials.,en_US
checkout.payment.provider.molliePaypal.descriptionText,Schließen Sie Ihren Einkauf sicher mit Ihrem PayPal-Konto ab.,de_DE
checkout.payment.provider.molliePaypal.descriptionText,Complete your purchase securely using your PayPal account.,en_US
checkout.payment.provider.mollieBankTransfer.descriptionText,Schließen Sie Ihren Einkauf sicher per Banküberweisung ab.,de_DE
checkout.payment.provider.mollieBankTransfer.descriptionText,Complete your purchase securely using bank transfer.,en_US
checkout.payment.provider.mollieKlarna.descriptionText,Schließen Sie Ihren Einkauf sicher mit Klarna ab.,de_DE
checkout.payment.provider.mollieKlarna.descriptionText,Complete your purchase securely with Klarna.,en_US
checkout.payment.provider.mollieKlarnaPayLater.descriptionText,Jetzt kaufen und später bezahlen mit Klarna.,de_DE
checkout.payment.provider.mollieKlarnaPayLater.descriptionText,Buy now and pay later with Klarna.,en_US
checkout.payment.provider.mollieKlarnaPayNow.descriptionText,Sofort bezahlen mit Klarna per Online-Banking.,de_DE
checkout.payment.provider.mollieKlarnaPayNow.descriptionText,Pay immediately with Klarna via online banking.,en_US
checkout.payment.provider.mollieKlarnaSliceIt.descriptionText,In Raten zahlen mit Klarna.,de_DE
checkout.payment.provider.mollieKlarnaSliceIt.descriptionText,Pay in installments with Klarna.,en_US
checkout.payment.provider.mollieEps.descriptionText,Bezahlen Sie sicher mit eps Online-Überweisung.,de_DE
checkout.payment.provider.mollieEps.descriptionText,Pay securely using eps online bank transfer.,en_US
checkout.payment.provider.mollieIdeal.descriptionText,Schnelle und sichere Zahlung mit iDEAL.,de_DE
checkout.payment.provider.mollieIdeal.descriptionText,Fast and secure payment with iDEAL.,en_US
checkout.payment.provider.mollieBancontact.descriptionText,Sicher bezahlen mit Bancontact.,de_DE
checkout.payment.provider.mollieBancontact.descriptionText,Secure payment with Bancontact.,en_US
checkout.payment.provider.mollieKbc.descriptionText,Bezahlen Sie direkt über KBC Online-Banking.,de_DE
checkout.payment.provider.mollieKbc.descriptionText,Pay directly via KBC online banking.,en_US
checkout.payment.provider.molliePayByBank.descriptionText,Bezahlen Sie per sicherer Banküberweisung.,de_DE
checkout.payment.provider.molliePayByBank.descriptionText,Pay using a secure bank transfer.,en_US
checkout.payment.provider.mollieApplePay.descriptionText,Schließen Sie Ihren Einkauf sicher mit Apple Pay ab.,de_DE
checkout.payment.provider.mollieApplePay.descriptionText,Complete your purchase securely using Apple Pay.,en_US
checkout.payment.provider.mollieTwintPayment.descriptionText,Complete your purchase securely with TWINT the Swiss mobile payment app.,en_US  
checkout.payment.provider.mollieTwintPayment.descriptionText,Schließen Sie Ihren Einkauf sicher mit TWINT ab der Schweizer Mobile Payment App.,de_DE  
checkout.payment.provider.mollieVippsPayment.descriptionText,Complete your purchase securely with Vipps the popular Norwegian mobile payment solution.,en_US  
checkout.payment.provider.mollieVippsPayment.descriptionText,Schließen Sie Ihren Einkauf sicher mit Vipps ab der beliebten norwegischen Mobile Payment Lösung.,de_DE  
checkout.payment.provider.mollieSatispayPayment.descriptionText,Complete your purchase securely with Satispay the simple and fast Italian mobile payment app.,en_US  
checkout.payment.provider.mollieSatispayPayment.descriptionText,Schließen Sie Ihren Einkauf sicher mit Satispay ab der einfachen und schnellen italienischen Zahlungs App.,de_DE  
checkout.payment.provider.mollieSwishPayment.descriptionText,Complete your purchase securely with Swish the leading mobile payment service in Sweden.,en_US  
checkout.payment.provider.mollieSwishPayment.descriptionText,Schließen Sie Ihren Einkauf sicher mit Swish ab dem führenden mobilen Bezahldienst in Schweden.,de_DE  
checkout.payment.provider.mollieMbWayPayment.descriptionText,Complete your purchase securely with MB WAY the fast and convenient Portuguese mobile payment solution.,en_US  
checkout.payment.provider.mollieMbWayPayment.descriptionText,Schließen Sie Ihren Einkauf sicher mit MB WAY ab der schnellen und bequemen portugiesischen Mobile Payment Lösung.,de_DE  
checkout.payment.provider.mollieTrustlyPayment.descriptionText,Complete your purchase securely with Trustly and pay directly from your bank account without registration.,en_US  
checkout.payment.provider.mollieTrustlyPayment.descriptionText,Schließen Sie Ihren Einkauf sicher mit Trustly ab und zahlen Sie direkt von Ihrem Bankkonto ohne Registrierung.,de_DE
checkout.payment.provider.mollieApplePay.descriptionText,Complete your purchase securely using Apple Pay.,en_US  
checkout.payment.provider.molliePrzelewy24.descriptionText,Bezahlen Sie sicher mit Przelewy24.,de_DE  
checkout.payment.provider.molliePrzelewy24.descriptionText,Pay securely with Przelewy24.,en_US  
checkout.payment.provider.mollieMultibanco.descriptionText,Bezahlen Sie sicher mit Multibanco.,de_DE  
checkout.payment.provider.mollieMultibanco.descriptionText,Pay securely with Multibanco.,en_US  
checkout.payment.provider.mollieBancomatPay.descriptionText,Bezahlen Sie sicher mit Bancomat Pay.,de_DE  
checkout.payment.provider.mollieBancomatPay.descriptionText,Pay securely with Bancomat Pay.,en_US  
checkout.payment.provider.mollieBizum.descriptionText,Bezahlen Sie sicher mit Bizum.,de_DE  
checkout.payment.provider.mollieBizum.descriptionText,Pay securely with Bizum.,en_US  
checkout.payment.provider.mollieBlik.descriptionText,Bezahlen Sie sicher mit BLIK.,de_DE  
checkout.payment.provider.mollieBlik.descriptionText,Pay securely with BLIK.,en_US  
checkout.payment.provider.molliePayconiq.descriptionText,Bezahlen Sie sicher mit Payconiq.,de_DE  
checkout.payment.provider.molliePayconiq.descriptionText,Pay securely with Payconiq.,en_US  
checkout.payment.provider.mollieBillie.descriptionText,Schließen Sie Ihren Einkauf sicher mit Billie ab.,de_DE  
checkout.payment.provider.mollieBillie.descriptionText,Complete your purchase securely using Billie.,en_US  
checkout.payment.provider.mollieIdealIn3.descriptionText,Schließen Sie Ihren Einkauf sicher mit iDEAL in3 ab.,de_DE  
checkout.payment.provider.mollieIdealIn3.descriptionText,Complete your purchase securely using iDEAL in3.,en_US  
checkout.payment.provider.mollieRiverty.descriptionText,Schließen Sie Ihren Einkauf sicher mit Riverty ab.,de_DE  
checkout.payment.provider.mollieRiverty.descriptionText,Complete your purchase securely using Riverty.,en_US  
checkout.payment.provider.mollieAlma.descriptionText,Schließen Sie Ihren Einkauf sicher mit Alma ab.,de_DE  
checkout.payment.provider.mollieAlma.descriptionText,Complete your purchase securely using Alma.,en_US
oms.state.pending,Ausstehende,de_DE
oms.state.pending,Pending,en_US
oms.state.expired,Expired,en_US
oms.state.expired,Abgelaufen,de_DE
oms.state.failed,Failed,en_US
oms.state.failed,Fehlgeschlagen,de_DE
oms.state.ready-for-shipment,Ready for shipment,en_US
oms.state.ready-for-shipment,Versandbereit,de_DE
oms.state.payment-pending-next-try,Payment pending next try,en_US
oms.state.payment-pending-next-try,Die Zahlung steht beim nächsten Versuch aus,de_DE
oms.state.refund-pending,Refund pending,en_US
oms.state.refund-pending,Rückerstattung steht aus,de_DE
oms.state.refund-processing,Refund processing,en_US
oms.state.refund-processing,Rückerstattungsabwicklung,de_DE
oms.state.refund-failed,Refund failed,en_US
oms.state.refund-failed,Rückerstattung fehlgeschlagen,de_DE
oms.state.refund-pending-next-try,Refund pending next try,en_US
oms.state.refund-pending-next-try,"Rückerstattung ausstehend,nächster Versuch",de_DE
oms.state.refund-closed,Refund closed,en_US
oms.state.refund-closed,Rückerstattung abgeschlossen,de_DE
oms.state.authorization-pending,Authorization pending,en_US
oms.state.authorization-pending,Autorisierung ausstehend,de_DE
oms.state.authorization-pending-next-retry,Authorization Pending Next Retry,en_US
oms.state.authorization-pending-next-retry,Autorisierung ausstehend Nächster Versuch,de_DE
oms.state.authorized,Authorized,en_US
oms.state.authorized,Autorisiert,de_DE
oms.state.authorization-failed,Authorization Failed,en_US
oms.state.authorization-failed,Autorisierung fehlgeschlagen,de_DE
oms.state.authorization-expired,Authorization Expired,en_US
oms.state.authorization-expired,Autorisierung abgelaufen,de_DE
oms.state.authorization-canceled,Authorization Canceled,en_US
oms.state.authorization-canceled,Autorisierung storniert,de_DE
oms.state.capture-pending,Capture pending,en_US
oms.state.capture-pending,Zahlungseinzug ausstehend,de_DE
oms.state.capture-pending-next-retry,Capture Pending Next Retry,en_US
oms.state.capture-pending-next-retry,Zahlungseinzug ausstehend Nächster Versuch,de_DE
oms.state.captured,Captured,en_US
oms.state.captured,Eingezogen,de_DE
oms.state.capture-failed,Capture Failed,en_US
oms.state.capture-failed,Zahlungseinzug fehlgeschlagen,en_US
mollie.mail.payment.confirmation.subject,Your payment has been confirmed,en_US
mollie.mail.payment.confirmation.subject,Ihre Zahlung wurde bestätigt,de_DE
mollie.mail.payment_confirmation.success_message,We have successfully received your payment and your order is now being processed.,en_US
mollie.mail.payment_confirmation.success_message,Wir haben Ihre Zahlung erfolgreich erhalten und Ihre Bestellung wird nun bearbeitet.,de_DE
mollie.mail.payment_confirmation.greeting,Dear,en_US
mollie.mail.payment_confirmation.greeting,Sehr geehrte,de_DE
mollie.mail.payment_confirmation.status_success,Payment Successful,en_US
mollie.mail.payment_confirmation.status_success,Zahlung erfolgreich,de_DE
mollie.mail.order_reference,Order reference,en_US
mollie.mail.order_reference,Bestellnummer,de_DE
mollie.mail.order_date,Order date,en_US
mollie.mail.order_date,Bestelldatum,de_DE
mollie.mail.total_amount,Total amount,en_US
mollie.mail.total_amount,Gesamtbetrag,de_DE
mollie.mail.payment_confirmation.thank_you,Thank you for your order!,en_US
mollie.mail.payment_confirmation.thank_you,Vielen Dank für Ihre Bestellung!,de_DE
mollie.mail.footer.automatic_message,"This is an automatic message, please do not reply.",en_US
mollie.mail.footer.automatic_message,"Dies ist eine automatische Nachricht, bitte antworten Sie nicht.",de_DE
```

## 6. Payment Methods Configuration

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
    'molliePrzelewy24Payment' => 'przelewy24',  
	'mollieMultibancoPayment' => 'multibanco',  
	'mollieBancomatPayPayment' => 'bancomatpay',  
	'mollieBizumPayment' => 'bizum',  
	'mollieBilliePayment' => 'billie',  
	'mollieRivertyPayment' => 'riverty',  
	'mollieIdealIn3Payment' => 'in3',  
	'mollieAlmaPayment' => 'alma',  
	'mollieBLIKPayment' => 'blik',  
	'molliePayconiqPayment' => 'payconiq',  
	'mollieTrustlyPayment' => 'trustly',  
	'mollieSwishPayment' => 'swish',  
	'mollieMbWayPayment' => 'mbway',  
	'mollieSatispayPayment' => 'satispay',  
	'mollieTwintPayment' => 'twint',  
	'mollieVippsPayment' => 'vipps',
]
```

### Supported Payment Methods

| Spryker Payment Method | Mollie ID | Description | Region |
|------------------------|-----------|-------------|--------|
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
| `molliePrzelewy24Payment` | `przelewy24` | Przelewy24 payment | PL |
| `mollieMultibancoPayment` | `multibanco` | Multibanco payment | PT |
| `mollieBancomatPayPayment` | `bancomatpay` | BANCOMAT Pay payment | IT |
| `mollieBizumPayment` | `bizum` | Bizum payment | ES, AD |
| `mollieBilliePayment` | `billie` | Billie payment | DE, AT, SE, NL, FR, NO, CH, UK, DK, FI, ES, IT |
| `mollieRivertyPayment` | `riverty` | Riverty payment | NL, BE, DE, AT |
| `mollieIdealIn3Payment` | `in3` | iDEAL in3 payment | NL |
| `mollieAlmaPayment` | `alma` | Alma payment | FR, BE |
| `mollieBLIKPayment` | `blik` | BLIK payment | PL |
| `mollieTrustlyPayment` | `trustly` | Trustly payment | AT, DE, DK, EE, FI, LT, LV, NL, NO, SE, UK |
| `mollieSwishPayment` | `swish` | Swish payment | SE |
| `mollieMbWayPayment` | `mbway` | MB Way payment | PT |
| `mollieSatispayPayment` | `satispay` | Satispay payment | AT,BG,HR,CY,CH,CZ,DK,EE,FI,FR,DE,GR,HU,IE,IT,LV,LT,LU,MT |
| `mollieTwintPayment` | `twint` | Twint payment | CH |
| `mollieVippsPayment` | `vipps` | Vipps payment | NO |

## 7. Backoffice Configuration

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

<!-- If using payment links -->
<mollie-payment-links>  
 <label>Mollie payment links</label>  
 <title>Mollie Payment Links</title>  
 <bundle>mollie</bundle>  
 <controller>payment-link</controller>  
 <action>index</action>  
 <visible>1</visible>  
</mollie-payment-links>
```

**Important:** Insert these blocks between the `<payment-method>` and `<shipment-method>` nodes in the navigation file.

**Example placement:**

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
    
	<mollie-payment-links>  
		<label>Mollie payment links</label>  
		<title>Mollie Payment Links</title>  
		<bundle>mollie</bundle>  
		<controller>payment-link</controller>  
		<action>index</action>  
		<visible>1</visible>  
	</mollie-payment-links>

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
3. The panel displays:
- All payment methods from your Mollie account
- Button for showing only enabled payment methods
- Button for clearing payment method cache
- Payment method status (active/inactive)
- Minimum/maximum transaction amount
- Payment method icons

### What You Can See in the Back Office Panel

<img src="../.github/images/payment_methods.png" alt="Mollie Payment Methods Backoffice Panel" width="600" />

The Mollie payment methods panel provides an overview of:

| Information | Description |
|-------------|-------------|
| **Payment Method Name** | Display name (e.g., "Credit Card", "PayPal", "iDEAL") |
| **Status** | "Activated" or "Not Activated" in your Mollie account |
| **Minimum Amount** | Minimum transaction amount for this method |
| **Maximum Amount** | Maximum transaction amount for this method |
| **Images** | Payment method icons |

> **Real-Time Sync**
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

## 8. Credit Card Components

Mollie provides secure, embeddable credit card components that allow customers to enter card details directly on your checkout page without the data passing through your servers.

### Enabling Components

```php
MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED => true
```

If this flag is set to false the credit card components will not be rendered in the checkout. Customers can still choose this payment method, but after placing the order they will be redirected to the hosted checkout page provided by Mollie to enter their credit card details.

### JavaScript Library Configuration

```php
MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC => 'https://js.mollie.com/v1/mollie.js'
```

### Benefits

| Benefit | Description |
|---------|-------------|
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

## 9. Wallet Payments

Digital wallet payments provide customers with quick, one-click checkout options using stored payment credentials.

### Wallet Configuration

```php
MollieConstants::MOLLIE_INCLUDE_WALLETS => ['applepay']
```

### Apple Pay Integration

Apple Pay allows customers to pay using Face ID, Touch ID, or passcode on supported Apple devices.

#### Setup Steps

1. Enable Apple Pay in your Mollie Dashboard
2. Add your domain for Apple Pay verification
3. Download and host the verification file on your domain
4. Complete the verification process
5. Add `'applepay'` to the `MOLLIE_INCLUDE_WALLETS` array
6. Test on compatible devices

> **Automatic Device Detection**
>
> Apple Pay will only display as a payment option when accessed from compatible devices and browsers. The integration automatically handles device and browser detection.

## 10. Payment Links

Payment Links allow merchants to generate shareable URLs that customers can use to complete a payment without going through the standard checkout flow. This is useful for invoicing, customer support scenarios, and manual order processing.

### Overview

A Mollie Payment Link is a hosted payment page that can be sent to a customer via email, SMS, or any other channel. When the customer opens the link, they are presented with Mollie's hosted payment page where they can select a payment method and complete the transaction.

**Key use cases:**

- Sending payment requests for phone or in-person orders
- Recovering abandoned checkouts
- Invoicing B2B customers
- Customer service-initiated payments

### API & Configuration Setup

#### Configuration Options

The following parameters can be set when generating a payment link:

| Parameter | Required | Description |
|-----------|----------|-------------|
| `amount` | Yes | The payment amount (value + currency) |
| `description` | Yes | A description shown on the hosted payment page |
| `redirectUrl` | No | Where to redirect the customer after payment |
| `webhookUrl` | No | Override webhook URL for this specific link |
| `expiresAt` | No | Expiry date/time for the link (ISO 8601) |

#### Generating a Payment Link

> **Webhook URL for Payment Links**
>
> Payment links use the next-gen webhook endpoint (`mollie/next-gen/webhook`). See [Section 13: Next-Gen Webhooks](#13-next-gen-webhooks) for full webhook configuration details.

### Backoffice: Creating & Managing Payment Links

> **Note:** Payment link management is visible in the Back Office panel. See [Section 5: Backoffice Configuration](#5-backoffice-configuration) for panel setup instructions.

#### Creating a Link

1. Log in to your Spryker Backoffice
2. Navigate to **Administration > Mollie payment links**
3. Click **Create Payment Link**
4. Fill in the required fields: amount, currency, description, and optional expiry date
- if no expiry date is set, default value will be used which is current datetime + 10080 seconds - configurable in config
5. Click **Create link** — the shareable URL will be displayed and can be copied or sent directly

#### Viewing Link Status

The Payment Links overview table shows:

| Column | Description |
|--------|-------------|
| **Mollie ID** | Unique Mollie identifier for the payment link |
| **ID Order** | ID of the order for which payment link is generated |
| **Description** | The description provided when the link was created |
| **Amount** | Payment amount |
| **Currency** | Payment currency |
| **Status** | `open`, `paid`, `expired`, or `canceled` |
| **Expires At** | Link expiry date/time (if set) |
| **Payment methods** | Available payment methods for payment link (if set) |
| **Created At** | Timestamp of link creation |

### Webhook Events for Payment Links

Payment links use Mollie's Next-Gen Webhooks rather than the legacy webhook mechanism. See [Section 13: Next-Gen Webhooks](#13-next-gen-webhooks) for full details on setup, payload structure, and event types.

#### Link-Specific Status Mapping

| Mollie Status           | Description | OMS Action |
|-------------------------|-------------|------------|
| `payment-link.open`     | Link created, awaiting customer action | None |
| `payment-link.paid`     | Payment successfully completed via link | Mark as paid |
| `payment-link.expired`  | Link expired before payment was made | Mark as expired |
| `payment-link.canceled` | Link was manually canceled | Mark as canceled |

#### Handling Expiry & Cancellation Events

If a payment link expires or is canceled, ensure your OMS handles these states gracefully. Configure the appropriate OMS transitions for `payment_expired` and `payment_cancelled` states as described in [Section 3.4](#34-configure-oms--mollie-payment-mapping).

### Testing Payment Links

1. Ensure `MOLLIE_TEST_MODE` is set to `true` and you are using a test API key
2. Generate a payment link via the Backoffice or API
3. Open the link in a browser — you will be taken to Mollie's hosted test payment page
4. Complete the payment using a [test card](#test-credit-cards) or any available test payment method
5. Verify the webhook is received and the link status updates to `paid` in the Back Office

> **Common Test Issues**
>
> - If the payment page does not load, verify your API key is a valid test key (`test_`)
> - If webhook status does not update, check your test webhook URL configuration (see [Section 9: Testing & Debugging](#9-testing--debugging))

## 11. Testing & Debugging

### Test Mode Setup

1. Set `MOLLIE_TEST_MODE` to `true`
2. Use your test API key (starts with `test_`)
3. Enable extensive debug logging: `MOLLIE_DEBUG_MODE => 'Extensive'`

### Test Credit Cards

| Card Number | Result |
|-------------|--------|
| `5555 5555 5555 4444` | ✅ Success |
| `4242 4242 4242 4242` | ✅ Success |
| `4111 1111 1111 1111` | ✅ Success |

**Test Card Details:**

- **Expiry Date:** Any future date
- **CVC:** Any 3 digits
- **Cardholder Name:** Any name

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

**Off Mode:** No logging (not recommended)

**Basic Mode Logs:**

- Request URLs and HTTP methods
- Error messages and codes

**Extensive Mode Logs** (includes all Basic logs plus):

- Complete request/response bodies (with sensitive data masked)
- Error messages and codes

#### Sensitive Data Masking

When `MOLLIE_DEBUG_MODE` is set to `'Extensive'`, sensitive data is automatically masked:

- **API Keys:** `test_abc*********************`
- **Email:** `john.doe@*****.com`
- **Phone:** `*******1234`
- **Names:** `John D.`

### Common Test Issues

| Issue | Possible Cause | Solution |
|-------|---------------|----------|
| Payment methods not appearing | Methods not enabled in Mollie Dashboard | Enable payment methods in test mode |
| Webhook not receiving updates | URL not accessible or incorrect | Test webhook URL manually, check firewall |
| API errors | Invalid API key or Profile ID | Verify credentials in Mollie Dashboard |
| Payment link not loading | Invalid or expired test API key | Verify test API key starts with `test_` |

## 12. Production Deployment

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

> **Production safety**
>
> - Never use test API keys in production
> - Always use HTTPS for all production URLs
> - Implement proper error handling and logging

## 13. Troubleshooting

### Payment methods not displaying at checkout

**Possible Causes:**

- Payment methods not enabled in Mollie Dashboard
- Incorrect Profile ID
- API key issues
- OMS mapping misconfiguration

**Solutions:**

1. Verify payment methods are enabled in Mollie Dashboard (Settings → Payment Methods)
2. Check that your Profile ID is correct
3. Verify API key has correct permissions
4. Check debug logs for API errors

### Webhooks not being received

**Possible Causes:**

- Webhook URL not accessible
- Firewall blocking Mollie's servers
- SSL certificate issues

**Solutions:**

1. Test webhook URL manually from external location
2. Whitelist Mollie's IP ranges in your firewall
3. Check SSL certificate validity
4. Review server logs for incoming webhook requests

### Credit card components not loading

**Possible Causes:**

- JavaScript library not loading
- HTTPS not enabled
- Content Security Policy (CSP) blocking
- Components not enabled in configuration

**Solutions:**

1. Ensure site is running on HTTPS
2. Check browser console for JavaScript errors
3. Update CSP to allow `js.mollie.com`
4. Verify Profile ID is correct

### Apple Pay not appearing

**Possible Causes:**

- Domain not verified with Apple
- Apple Pay not enabled in Mollie Dashboard
- Missing from wallet configuration

**Solutions:**

1. Complete Apple Pay domain verification in Mollie Dashboard
2. Verify Apple Pay is enabled for your account
3. Test on compatible Apple device with Safari or Chrome
4. Check that Apple Pay is enabled in device settings

### Payment link not generating

**Possible Causes:**

- Insufficient API key permissions
- Missing required parameters (amount, description)
- Invalid expiry date format

**Solutions:**

1. Verify your API key has payment link creation permissions in the Mollie Dashboard
2. Ensure `amount.value` is a string with two decimal places (e.g., `"25.00"`)
3. Ensure `expiresAt` follows ISO 8601 format if provided

## 14. Webhook Handling

Webhooks are asynchronous notifications sent by Mollie to your server when payment status changes occur. Proper webhook handling is critical for accurate order processing and payment status updates.

### How Webhooks Work

1. **Payment Status Changes:** When a payment status changes (e.g., paid, cancelled, expired), Mollie sends an HTTP POST request to your webhook URL
2. **Webhook Receipt:** Your server receives the webhook notification
3. **Validation:** The webhook payload is validated to ensure it's from Mollie
4. **Status Retrieval:** Your server fetches the latest payment details from Mollie API
5. **Order Update:** The order status is updated based on the payment status
6. **Response:** Your server responds with HTTP 200 to acknowledge receipt

### Webhook Configuration

Webhooks are configured via the webhook URL settings:

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

**Request:**

```
POST /mollie/webhook HTTP/1.1
Host: www.yourstore.com
Content-Type: application/x-www-form-urlencoded

id=tr_WDqYK6vllg
```

**Important:** The webhook contains only the payment ID. Your application must then make an API call to Mollie to retrieve the full payment details.

### Payment Status Mapping

Mollie payment statuses map to OMS states as follows:

| Mollie Status | Description | OMS Action | Typical Next State |
|---------------|-------------|------------|-------------------|
| `open` | Payment created, awaiting customer action | None | `payment_pending` |
| `pending` | Payment started, awaiting confirmation | None | `payment_pending` |
| `authorized` | Payment authorized, awaiting capture | Authorize recorded | `payment_authorized` |
| `paid` | Payment successfully completed | Mark as paid | `payment_completed` |
| `failed` | Payment failed | Mark as failed | `payment_failed` |
| `canceled` | Payment cancelled by customer | Mark as cancelled | `payment_cancelled` |
| `expired` | Payment expired (timeout) | Mark as expired | `payment_expired` |

> **Payment Links:** Payment links use Next-Gen Webhooks with an expanded event model. See [Section 13: Next-Gen Webhooks for Payment Links](#13-next-gen-webhooks-for-payment-links) for link-specific status handling.

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

> **Best Practice**
>
> Always respond with HTTP 200 immediately, even if processing hasn't completed. Process the webhook asynchronously if needed to avoid timeouts.

## 15. Next-Gen Webhooks

### What Are Next-Gen Webhooks?

Mollie's **Next-Gen Webhooks** are a modernized event delivery system replacing the legacy approach. Instead of sending only a resource `id` and requiring your server to make a follow-up API call to find out what changed, next-gen webhooks deliver **structured, signed event payloads** directly to your endpoint — including an optional full snapshot of the changed resource.

### Key Improvements Over Legacy Webhooks

| Feature | Legacy | Next-Gen |
|---------|--------|----------|
| Payload | Resource `id` only | Full event + optional resource snapshot |
| Signature verification | Not supported | `Mollie-Signature` header on every request |
| Event types | Payment status only | Expanded — includes payment links, disputes, etc. |
| Webhook registration | Per-resource `webhookUrl` param | Centrally managed via Webhooks API |
| Security model | Trust the ID, fetch manually | Verify signature, consume payload directly |

### Setup & Configuration

#### Prerequisites

- A Mollie account with an active profile.
- An API key (`test_` or `live_`).
- A publicly reachable HTTPS endpoint on your server.

> **Internal note:** For local development, use a tunnelling tool such as [ngrok](https://ngrok.com) or [Expose](https://expose.dev) to make your local endpoint reachable by Mollie.

#### Register a Webhook

You can register your endpoint via the Webhooks API or via the Mollie Dashboard. A single webhook subscription can cover multiple event types.

#### Payment Link Event Types

| Event Type | When it fires |
|------------|---------------|
| `payment_link.paid` | A customer successfully paid via the link. |

### Security & Validation

#### Signature Verification

Every next-gen webhook request includes a `Mollie-Signature` header. **Always verify this header before processing the event.** Skipping this check means any actor with your endpoint URL can send forged requests.

The signature is an HMAC-SHA256 hash of the raw request body, signed with your webhook secret.

**PHP example:**

```php
function verifyMollieSignature(
    string $rawBody,
    string $signatureHeader,
    string $webhookSecret
): bool {
    $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $webhookSecret);
    return hash_equals($expected, $signatureHeader);
}

// In your webhook controller:
$rawBody   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_MOLLIE_SIGNATURE'] ?? '';

if (!verifyMollieSignature($rawBody, $signature, $_ENV['MOLLIE_WEBHOOK_SECRET'])) {
    http_response_code(401);
    exit;
}

$event = json_decode($rawBody, true);
```

> Always use `hash_equals()` instead of `===` for the comparison. This prevents **timing attacks**, where an attacker could infer the correct signature by measuring how long your comparison takes.

#### Validation Checklist

Before acting on any incoming webhook, your handler should verify all of the following:

**Signature**
- [ ] `Mollie-Signature` header is present.
- [ ] HMAC-SHA256 of the raw body matches the header value.
- [ ] Comparison uses `hash_equals()`, not `===`.

#### Respond Fast, Process Async

Mollie expects a `200 OK` response quickly. If your handler takes too long, Mollie may consider the delivery failed and retry.

The recommended pattern:

1. Verify the signature.
2. Validate the payload structure.
3. Persist to database.
4. Return `200 OK` immediately.

#### Security Best Practices

- **Store webhook secrets securely** — never hardcode them in source code. Use environment variables or a secrets manager.
- **Use HTTPS only** — Mollie will not deliver webhooks to plain HTTP endpoints in production.
- **Do not expose internal errors** — return a generic `500` on unexpected failures rather than a detailed error message.
- **Rotate secrets periodically** — update your webhook secret.

## 16. Webhook Error Troubleshooting

### Common Webhook Issues

#### Issue 1: Webhooks Not Being Received

**Symptoms:**

- Payment status not updating in your system
- Orders stuck in "payment pending" state
- No webhook logs in application logs

**Diagnostic Steps:**

1. **Check Webhook URL Configuration:**

```bash
# Verify webhook URL is set correctly
MOLLIE_WEBHOOK_URL config/Shared/config_default.php
```

2. **Test URL Accessibility:**

```bash
# Test from external location
curl -X POST https://www.yourstore.com/mollie/webhook \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "id=tr_test123"
```

Expected: HTTP 200 response

3. **Check Firewall Rules:**

- Ensure Mollie's IP addresses are not blocked
- Check cloud provider security groups
- Verify web application firewall (WAF) rules

**Common Causes & Solutions:**

| Cause | Solution |
|-------|----------|
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

1. **Enable Extensive Logging:**

```php
MollieConstants::MOLLIE_DEBUG_MODE => 'Extensive'
```

2. **Check Application Logs**

3. **Review Error Details**

4. **Test Payment Status API:**

```bash
curl -X GET https://api.mollie.com/v2/payments/tr_WDqYK6vllg \
  -H "Authorization: Bearer test_yourapikey"
```

**Common Causes & Solutions:**

| Cause | Solution |
|-------|----------|
| Invalid API key | Verify API key in environment variables |
| Database connection failure | Check database connectivity |
| OMS state machine error | Review OMS configuration for payment method |
| Order not found | Verify order exists and payment ID mapping |
| Timeout during processing | Implement async processing |
| PHP errors / exceptions | Review error logs and fix code issues |

### Getting Help with Webhook Issues

When contacting support, provide:

1. **Correlation ID:** Specific transaction identifier
2. **Payment ID:** Mollie payment ID (`tr_*`)
3. **Timestamp:** When issue occurred
4. **Logs:** Relevant log entries with correlation ID
5. **Configuration:** Current webhook URL configuration
6. **Test Results:** Results of manual webhook tests
7. **Environment:** Production, staging, or development

**Mollie Support Resources:**

- Technical Documentation: docs.mollie.com/overview/webhooks
- API Status: status.mollie.com
- Support Portal: help.mollie.com

## Getting Help

| Resource | Link | Use For |
|----------|------|---------|
| Mollie API Documentation | docs.mollie.com | API reference, payment methods, features |
| Mollie Support | help.mollie.com | Account issues, payment method questions |
| Spryker Documentation | docs.spryker.com | Spryker platform, OMS configuration |