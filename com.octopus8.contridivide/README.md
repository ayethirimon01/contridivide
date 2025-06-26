# com.octopus8.contridivide
(*FIXME: In one or two paragraphs, describe what the extension does and why one would download it. *)

This is an [extension for CiviCRM](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/), licensed under [AGPL-3.0](LICENSE.txt).

## Objective

This custom CiviCRM extension enhances contribution tracking by automatically generating a **customized Receipt ID** for each new contribution. Once enabled, it creates a custom field called **"Receipt ID"** for contributions, which is populated automatically based on a configurable format defined by the user.

## Key Features

- Automatically creates a custom "Receipt ID" field for Contributions upon extension activation.
- Adds a new navigation item under **Contributions > Receipt ID Prefix** to configure settings.
- Allows users to customize the Receipt ID format with the following options:
  - **Prefix** (e.g., `OCTOPUS8`, `O8`, `AWWA`)
  - **Receipt Number Length** (e.g., 6-digit: `000001`)
  - Option to **Include Year** in the Receipt ID
  - Option to **restart numbering each year**
  - Option to **Include Financial Types** in the Receipt ID
  - **Customize formats for different Financial Types**
  - **Reorder components** (e.g., prefix, year, number) via drag-and-drop
  - Add a **delimiter** (e.g., `-`, `/`) between components
  - Live **preview** of the generated Receipt ID before saving

## How It Works

When a new contribution is created, the system will automatically assign a unique Receipt ID to the contribution, constructed based on the user-defined format.

## How to Use the Extension

1. **Enable the Extension**
   - Go to **Administer > System Settings > Extensions**
   - Install and enable the `Contridivde` extension

2. **Configure Receipt ID Format**
   - Navigate to **Contributions > Receipt ID Prefix**
   - Choose or define:
     - Prefix
     - Number length
     - Year inclusion
     - Whether to reset numbering each year
     - Financial type-specific formatting
     - Delimiter character
   - Drag and drop format blocks to define the order
   - Preview the generated Receipt ID format
   - Click **Save** to apply the configuration

3. **Create a Contribution**
   - When a new contribution is added via any method (manual, online, import), the "Receipt ID" field will be automatically populated based on your configured format.

## Additional Behavior

- If the configuration form is submitted again, Receipt IDs will be generated **based on the latest saved settings**.
- If the user **does not define any settings for certain financial types** (e.g., NTDR or TDR), but defines a format only for **DIK**, then:
  - Contributions with financial type **DIK** will have their own separate receipt number sequence.
  - Contributions with **undefined financial types** (like NTDR and TDR in this case) will be grouped together and share the **same** receipt number sequence.
- If the configuration form has **never been submitted**, the system will fall back to default prefixes (`NTDR`, `TDR`, `DIK`) based on financial type to generate Receipt IDs.
- Any financial type other than NTDR, TDR, or DIK will be treated as TDR.

## Notes

- This extension ensures that all Receipt IDs are unique and formatted consistently.
- It supports both simple and complex numbering schemes to suit different organizational requirements.
- Settings are fully customizable and stored for each financial type if needed.

## Known Issues

(* FIXME *)
