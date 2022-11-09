## Exc App
This application is a currency exchange application. It was built in a way
that imagining it would be used by a exchange office cashier.

## Status
Under early development. This was done for an interview, I will still keep
developing it as my Public Github is pretty much empty & I want to have a nice
public project.

## Installation Guide
### Requirements:
1. Docker (with WSL2.0 Opt on Windows)
2. Composer (Run composer from local machine, and Laravel commands from container)

### Steps to Run
1. Pull the project.
2. Run composer install
3. Run ./vendor/bin/sail up to get Docker Container running
4. Set your .env file according to your needs. (Don't forget to set Exchange Api Url & Key)
5. Run ./vendor/bin/sail artisan migrate
6. Run ./vendor/bin/sail artisan currency:sync
7. Go localhost (or where you are hosting depending on your docker-compose.yml)
8. Click **"REGISTER"** on top right side, register & login to your account.
9. Viola! You are on the dashboard!

### Test Requirements
Your phpunit.xml file should have an API key.


## What is missed?
1. More unit tests
   1. **What I would like to improve**: more functions need to be unit tested
      1. **How I would like to improve and why**: I need to focus on TDD more.
2. The application cannot handle provider errors well.
   1. **What I would like to improve**: Better response handling
      1. **How I would like to improve and why**: ExchangeRatesIO service sometimes timeouts, or the app itself has sync issues. I need to focus more on response & exception handling.
3. SyncCurrencyTest is not sufficient
   1. **What I would like to improve**: Data mocking on test
      1. **How I would like to improve and why**: Even though this will not prevent the app from running, on initial setup, that function is critical. So proper unit test needs to be rewritten.
4. Transaction table's amount column's decimal(15,2)  is not sufficient
   1. **What I would like to improve**: To be able to save transactions for BTC safely
      1. **How I would like to improve and why**: bigger decimal point but a memory efficient column data size
5. A good UI
   1. **What I would like to improve**: The UI in general. Also I cannot show errors on UI.
      1. **How I would like to improve and why**: Maybe pulling every request to Ajax version. To do this, I need to improve my frontend knowledge
      
