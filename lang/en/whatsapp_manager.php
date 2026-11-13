<?php

declare(strict_types=1);

return [
    'title' => 'WhatsApp numbers',
    'description' => 'The customer service numbers of the WhatsApp button on the website. Only active numbers are shown, in this order. With no active number the button is hidden.',
    'create' => 'Add number',
    'create_title' => 'Add number',
    'create_description' => 'Add a customer service number. Visitors will chat with it on WhatsApp.',
    'edit_title' => 'Edit number',
    'edit_description' => 'Change the name or the number.',
    'edit' => 'Edit',
    'activate' => 'Turn on',
    'deactivate' => 'Turn off',
    'move_up' => 'Up',
    'move_down' => 'Down',
    'delete' => 'Delete',
    'confirm_delete' => 'Delete this number? It disappears from the website. To hide it for a while, turn it off instead.',
    'actions' => 'Actions',
    'empty' => 'No numbers yet. The WhatsApp button stays hidden on the website until you add one.',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'fields' => [
        'name' => 'Name',
        'number' => 'WhatsApp number',
        'status' => 'Status',
    ],
    'hints' => [
        'number' => 'With the country code, for example +62 812-0000-0001. Spaces, dashes and the plus sign are ignored.',
    ],
    'status' => [
        'active' => 'On',
        'inactive' => 'Off',
    ],
    'messages' => [
        'created' => 'Number added.',
        'updated' => 'Changes saved.',
        'deleted' => 'Number deleted.',
        'bad_number' => 'Enter the full number with the country code, 8 to 15 digits, for example +62 812-0000-0001.',
    ],
];
