# Contribution Guide

Contributions of all kinds are welcome and appreciated via Pull Requests.

## Testing

To run the provided tests, you will need to have a valid AbuseIPDB API key in your `.env.testing` file.

1. Copy the `.env.testing.example` file to `.env.testing`.
2. Fill `ABUSEIPDB_API_KEY` 
3. Fill `BAD_IP_TO_TEST`, you can use an IP address that has been reported to AbuseIPDB.
4. Run `composer test`.

> [!NOTE]
> The AbuseIPDB API will throw an error if an IP address is reported more than once in a period of 15 minutes, so any 
> report endpoint tests will throw errors. Any tests that do not use the report endpoint will still work without any 
> waiting period.

## Submitting a Pull Request

1. Fork the repository.
2. Make your changes and commit them with descriptive messages.
3. Push your branch to your forked repository.
4. Open a Pull Request to the main repository, explaining:
    - The issue your PR addresses (if applicable).
    - The solution you've implemented.
    - Any remaining questions or considerations.

Thank you for your contribution!