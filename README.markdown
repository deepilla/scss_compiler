# JIT SCSS Compiler #

"Just in time" SASS compiler for Symphony CMS. Supports SCSS syntax only.

- Version: 1.0
- Date: 25th February 2015
- Requirements: Symphony 2.3 or later
- Author: deepilla, hello@deepilla.com
- GitHub Repository: https://github.com/deepilla/scss_compiler

## Synopsis

A fork of Nils Werner's [SASS Compiler](http://symphonyextensions.com/extensions/sass_compiler/) extension with the following changes:

- The [phpsass](https://github.com/richthegeek/phpsass) compiler library has been replaced with [scssphp](https://github.com/leafo/scssphp/)
- If compilation fails, the error message is output to the CSS file so you can see what went wrong

## Why fork the SASS Compiler extension?

The [SASS Compiler](http://symphonyextensions.com/extensions/sass_compiler/) discards compilation errors which makes diagnosing problems a pain. Initially I just wanted to expose the error messages and maybe upgrade to the latest version of [phpsass](https://github.com/richthegeek/phpsass). But after some investigation I decided to switch to the [scssphp](https://github.com/leafo/scssphp/) library because:

- it implements a slightly newer version of SASS (3.2.12 vs "approx 3.2")
- it appears to be more actively maintained
- it has a [server mode](http://leafo.net/scssphp/docs/#scss_server) which caches the compiled CSS rather than recompiling on every request. Implementing this would make the extension suitable for production environments

On the downside, [scssphp](https://github.com/leafo/scssphp/) doesn't support [the old SASS syntax](http://sass-lang.com/documentation/file.INDENTED_SYNTAX.html). But you weren't really using that, were you?

## Installation

See [*Install an Extension*](http://www.getsymphony.com/learn/tasks/view/install-an-extension/) in the Symphony docs.

1. Uninstall the [SASS Compiler](http://symphonyextensions.com/extensions/sass_compiler/) extension if it's installed
2. Install the SCSS Compiler extension per [the docs](http://www.getsymphony.com/learn/tasks/view/install-an-extension/)
3. Install the [scssphp](https://github.com/leafo/scssphp/) library into `lib/scssphp` either by manual copy or `git submodule add`

**Note**: Installing the SASS Compiler and the SCSS Compiler at the same time may result in unexpected behaviour due to conflicting `.htaccess` rules. Use one or the other but not both.

## Usage

### Basics

This extension is a drop-in replacement for the [SASS Compiler](http://symphonyextensions.com/extensions/sass_compiler/). Include your SCSS stylesheet, say `workspace/assets/sass/style.scss`, using:

	<link rel="stylesheet" href="/scss/assets/sass/style.scss" />

Your SCSS is compiled on the fly when the page is loaded. A copy of the compiled CSS is saved in `/path/to/project/manifest/cache/scss_compiler`. Note that this file is never used - the SCSS is recompiled on every page load whether it has changed or not.

Files imported with `@import` are assumed to be in the same directory as the parent file.

If an error occurs during compilation the CSS file will contain the error message as a comment.

### Warning: Do Not Use In Production

Because your SCSS is compiled on every page load, you should **only use this extension for development**. Use the compiled CSS on the live site. You can use a template to switch between the two, e.g.

```XSLT
<!-- Are we in Production? -->
<xsl:variable name="production" select="{test for production environment goes here}" />

<!-- Compile SASS in Development, use compiled CSS in Production -->
<xsl:template name="compile-sass">
    <xsl:param name="filename" />
    <xsl:choose>
        <xsl:when test="$production">
            <link rel="stylesheet" href="/workspace/assets/css/{$filename}.css" />
        </xsl:when>
        <xsl:otherwise>
            <link rel="stylesheet" href="/scss/assets/sass/{$filename}.scss" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>
```

You are responsible for placing the compiled CSS files in the correct location (`/workspace/assets/css` in this example).

Implementing the [server mode](http://leafo.net/scssphp/docs/#scss_server) of [scssphp](https://github.com/leafo/scssphp/) would make the extension usable in Production. It compiles the SCSS once, caches the resulting CSS, and serves the cached files on subsequent page loads, only recompiling if the SCSS changes. It's on the todo list...

## TODO

1. Look into [scssphp's server mode](http://leafo.net/scssphp/docs/#scss_server) for CSS-caching functionality
