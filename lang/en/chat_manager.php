<?php

declare(strict_types=1);

return [
    'title' => 'Chat assistant',
    'description' => 'Questions the chat on the website answers. Each answer can lead to follow-up questions, as deep as you like. Visitors see only the languages you fill in.',
    'create' => 'Add question',
    'create_title' => 'Add question',
    'create_description' => 'Write the question visitors tap, and the answer they get, in each language you want to offer.',
    'edit_title' => 'Edit question',
    'edit_description' => 'Change the question, the answer or the link in any language.',
    'follows' => 'Follow-up to:',
    'add_follow_up' => 'Add follow-up',
    'edit' => 'Edit',
    'move_up' => 'Up',
    'move_down' => 'Down',
    'delete' => 'Delete',
    'confirm_delete' => 'Delete this question, its answers in every language and all its follow-up questions? This cannot be undone.',
    'actions' => 'Actions',
    'level' => 'Question :number',
    'empty' => 'No questions yet. The chat button stays hidden on the website until you add one.',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'fields' => [
        'question' => 'Question',
        'answer' => 'Answer',
        'link_label' => 'Link text',
        'link_url' => 'Link address',
        'languages' => 'Languages',
    ],
    'hints' => [
        'languages' => 'Fill in only the languages you want. A language left empty is not shown to visitors in that language, and neither are its follow-up questions.',
        'link_url' => 'Optional. A page of this site such as /en/contact, or a full address starting with https://.',
    ],
    'messages' => [
        'created' => 'Question added.',
        'updated' => 'Changes saved.',
        'deleted' => 'Question deleted.',
        'language_required' => 'Fill in at least one language.',
        'bad_link' => 'Use a page of this site starting with / or an address starting with http:// or https://.',
    ],
];
