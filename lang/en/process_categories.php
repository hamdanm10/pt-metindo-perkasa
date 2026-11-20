<?php

declare(strict_types=1);

return [
    'title' => 'Process categories',
    'create' => 'Add process category',
    'create_title' => 'Add process category',
    'description' => 'Keep the list of process categories shown on the website, with their texts in each language.',
    'create_description' => 'Fill in the name, then write each language yourself. English is required.',
    'edit_description' => 'Change the name or the texts of this process category.',
    'edit_title' => 'Edit process category',
    'move_up' => 'Up',
    'move_down' => 'Down',
    'language_filled' => 'Written',
    'language_empty' => 'Not written',
    'edit' => 'Edit',
    'search' => 'Search name',
    'actions' => 'Actions',
    'empty' => 'No process categories yet.',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'status' => 'Status',
    'processes' => 'Processes',
    'active' => 'Active',
    'inactive' => 'Hidden',
    'fields' => [
        'name' => 'Name',
        'slug' => 'Address (slug)',
        'active' => 'Show on the website',
        'languages' => 'Languages',
        'description' => 'Description',
    ],
    'hints' => [
        'name' => 'The name shown on the website. The page address is made from it when the category is added.',
        'active' => 'Turn off to hide the category and its processes without deleting them.',
        'languages' => 'A language without any text is not saved. A language without a translation shows the English text.',
    ],
    'messages' => [
        'name_unusable' => 'This name gives an address that another process category already uses or cannot be made into an address. Use a different name.',
        'created' => 'Process category added.',
        'updated' => 'Changes saved.',
        'default_language_required' => 'Write the :language description: it is shown in languages without a translation.',
    ],
];
