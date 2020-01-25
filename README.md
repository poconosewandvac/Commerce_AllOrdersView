# All Orders View for Commerce

Add a new section to view all orders (draft, processing, completed) in the Modmore Commerce dashboard. Requires a valid [Commerce license](https://modmore.com/commerce) to use.

Note that this all orders section sorts orders by `created_on`, not `received_on` like processing and completed orders (since the order may be a draft which has never been received).

## Setup

1. Download the transport package and upload it into MODX under Extras -> Installer
2. Enable the Commerce module in the Commerce dashboard under Configuration -> Modules -> All Orders View
3. Go to the "Orders" tab. View the new "All" option displays

## Development

Setup dependencies:

```
php _bootstrap/index.php
```

Build the package:

```
php _build/build.transport.php
```