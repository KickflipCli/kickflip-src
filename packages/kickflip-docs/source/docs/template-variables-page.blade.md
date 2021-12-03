---
extends: layouts.documentation
section: docs_content
title: Page Data
---

Unlike the `SiteData` which is a single instance global for all templates, every page gets a unique `PageData`.
This is constructed for each page as a step before it is rendered.  

Each `PageData` is constructed of basic meta-data for rendering the page and embedded `SourcePageMetaData` about the source file.

## Built-in Properties
These are all the built-in public properties that exist on `PageData`.
Each one is set from the corresponding FrontMatter section of data.

- `public string $url` - The page's URL.  
Can be set via FrontMatter, or implicitly defined based on the source file.
- `public string $title` - The page's title.  
  Can be set via FrontMatter, or implicitly defined based on the source file.
- `public ?string $description = null` - The page's description.  
An optional property for the page's description.
- `public ?string $extends = null` - What layout the source extends.  
An optional property of which layout to extend with the source content.
- `public ?string $section = null` - The section to insert the content into.  
When an `extends` property is set, this must be set to define which section to put the content in.
- `public bool $autoExtend = true` - If the page should extend a layout if no `extends` set.  
An optional bool value for if the source extends a layout. 
If set false and no `layout` set, then the markdown will render as raw HTML without a layout.


### Additional Dynamic Properties

Any additional FrontMatter data that gets parsed from the source file will be put into dynamic properties.
For instance, this markdown:  

```markdown
---
food: 'Burger'
----
Stuff and such.
```

Would render into a dynamic property of: `PageData::$food = 'burger'` accessible by the view engine. These additional data fields can be used directly on the page, or for other needs with:
`$pageData->food`.

## Configuring Default behaviour

When you use the same layout for every source file defining the `extends` and `section` can get repetitive.
Or even add room for error! That's where the `autoExtend` feature comes into play.

On `PageData` there exist two static properties used for defaults of this feature.
These are:

```php
    public static string $defaultExtendsView = 'layouts.master';
    public static string $defaultExtendsSection = 'body';
```

You can either use these as suggested values when you setup your views.
Or, you can override these values in your sites `bootstrap.php` file. Like:

> config/bootstrap.php
```php
\Kickflip\Models\PageData::$defaultExtendsView = 'layouts.blog';
\Kickflip\Models\PageData::$defaultExtendsSection = 'blog_content';
```