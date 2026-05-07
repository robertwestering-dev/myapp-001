<?php

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'array' => 'The :attribute field must be an array.',
    'boolean' => 'The :attribute field must be true or false.',
    'date' => 'The :attribute field must be a valid date.',
    'url' => 'The :attribute field must be a valid URL.',
    'alpha_dash' => 'The :attribute field must only contain letters, numbers, dashes, and underscores.',
    'unique' => 'The :attribute has already been taken.',
    'max' => [
        'string' => 'The :attribute field must not be greater than :max characters.',
        'array' => 'The :attribute field must not have more than :max items.',
    ],
    'attributes' => [
        'slug' => 'slug',
        'cover_image_url' => 'cover image URL',
        'tags' => 'tags',
        'published_at' => 'publish date',
        'is_published' => 'publication status',
        'is_featured' => 'featured status',
        'title.nl' => 'Dutch title',
        'title.en' => 'English title',
        'title.de' => 'German title',
        'excerpt.nl' => 'Dutch excerpt',
        'excerpt.en' => 'English excerpt',
        'excerpt.de' => 'German excerpt',
        'content.nl' => 'Dutch content',
        'content.en' => 'English content',
        'content.de' => 'German content',
    ],
];
