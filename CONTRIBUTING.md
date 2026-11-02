# Contributing

Thank you for considering a contribution to AVCL Blocks.

## Before you start

- Search [existing issues](https://github.com/ankur0007/wp-gutenberg-blocks/issues)
  before filing a bug or feature request.
- For larger changes, open an issue first to discuss the proposed approach.
- Never include credentials, access tokens, private store data, or other secrets
  in an issue, pull request, test, or screenshot.

## Development setup

1. Install Node.js 20 or later and PHP 7.4 or later.
2. Clone the repository and run `npm install`.
3. Run `npm start` while developing blocks.
4. Run `npm run build` before submitting changes. Generated files in `build/`
   are tracked and should be included when the block output changes.

## Pull requests

- Keep changes focused and follow existing WordPress block conventions.
- Include or update documentation when behavior or setup changes.
- Add tests when practical, and include steps to reproduce bug fixes.
- Verify `npm run build` succeeds and changed PHP files pass `php -l`.
- Describe the change and any relevant compatibility or configuration impact.

## Reporting security issues

Do not post credentials or exploit details in public issues. Contact the
repository owner privately through GitHub to coordinate a responsible report.
