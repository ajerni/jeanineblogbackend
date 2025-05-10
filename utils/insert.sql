INSERT INTO blog_posts (
    title,
    slug,
    excerpt,
    content,
    featured_image,
    published_date,
    updated_date,
    tags,
    published
) VALUES (
    'My shiny apple',
    'my-shiny-apple',
    'This is a test post to see how images work.',
    'With an apple for example. Stored at imagekit.io. In my mywine folder. And I am the content of this post. I am a test post to see how images work.',
    'https://ik.imagekit.io/mywine/andiblog/tr:w-800,h-400/apfel.jpg',
    NOW(),
    NOW(),
    '["Andi", "test"]',
    1
);