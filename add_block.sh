#!/bin/bash

# Prompt for block name
read -p "Enter block name: " block_name

# Exit if input is empty
if [[ -z "$block_name" ]]; then
    echo "Error: Block name is required."
    exit 1
fi

# Kebab-case for filenames / ACF slug
block_kebab=$(echo "$block_name" | tr '[:upper:]' '[:lower:]' | tr ' ' '-')

# Underscore for ACF keys and JSON
block_slug=$(echo "$block_name" | tr '[:upper:]' '[:lower:]' | tr ' ' '_')

# Define file paths
php_file="./page-templates/blocks/${block_kebab}.php"
scss_file="./src/sass/theme/blocks/${block_kebab}.scss"
blocks_scss="./src/sass/theme/blocks/_blocks.scss"
blocks_php="./inc/lc-blocks.php"
acf_json_file="./acf-json/group_${block_slug}.json"

# Exit if any of the files already exist
if [[ -e "$php_file" || -e "$scss_file" || -e "$acf_json_file" ]]; then
    echo "Error: One or more of the following files already exist:"
    [[ -e "$php_file" ]] && echo " - $php_file"
    [[ -e "$scss_file" ]] && echo " - $scss_file"
    [[ -e "$acf_json_file" ]] && echo " - $acf_json_file"
    exit 1
fi

# Create PHP and SCSS files
touch "$php_file"
touch "$scss_file"
echo "Created: $php_file"
echo "Created: $scss_file"

# Add import statement to _blocks.scss if not already present
if ! grep -q "@import '${block_kebab}';" "$blocks_scss"; then
    echo "@import '${block_kebab}';" >> "$blocks_scss"
    echo "Added @import to $blocks_scss"
else
    echo "Import already exists in $blocks_scss"
fi

# Define the block registration code
block_code="\n        acf_register_block_type(array(\n            'name'                => '$block_kebab',\n            'title'               => __('$block_name'),\n            'category'            => 'layout',\n            'icon'                => 'cover-image',\n            'render_template'     => 'page-templates/blocks/$block_kebab.php',\n            'mode'                => 'edit',\n            'supports'            => array('mode' => false, 'anchor' => true, 'className' => true),\n        ));\n"

# Insert block registration code into lc-blocks.php
if grep -q "function acf_blocks()" "$blocks_php"; then
    sed -i "/if ( function_exists( 'acf_register_block_type' ) ) {/a\\$block_code" "$blocks_php"
    echo "Added block registration to $blocks_php"
else
    echo "acf_blocks() function not found in $blocks_php. Please check the file."
fi

# Create ACF JSON stub
acf_json_content="{
    \"key\": \"group_${block_slug}\",
    \"title\": \"$block_name\",
    \"fields\": [
        {
            \"key\": \"field_${block_slug}_title\",
            \"label\": \"$block_name\",
            \"name\": \"title\",
            \"type\": \"message\"
        }
    ],
    \"location\": [
        [
            {
                \"param\": \"block\",
                \"operator\": \"==\",
                \"value\": \"acf\\/$block_kebab\"
            }
        ]
    ],
    \"active\": 1,
    \"description\": \"\"
}"

echo "$acf_json_content" > "$acf_json_file"
echo "Created ACF field group JSON: $acf_json_file"
