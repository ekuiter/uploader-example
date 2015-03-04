# uploader-example

Another project for my seminar course "Creating web sites"
(see [session-example](https://github.com/ekuiter/session-example)).
This one is an uploader made with PHP, jQuery and famo.us.

You can upload multiple files at once (with HTML5) and configure which file
types / sizes / image dimensions to allow.

## Installation

Just clone this repository onto your PHP server. If you want to create thumbnails
(`ResizeHook`) or check image types (`IsImagePermission`) you need the GD
Library to be installed.

Next, rename `config.template.php` and `config.template.js` to `config.php`
and `config.js` respectively. Open the files and configure them as needed.

By default, files are uploaded to the `uploads` folder, you can change that
or you can even upload to a remote FTP server.

Keep the `tmp` folder if you want to create thumbnails (`ResizeHook`)!

## About the project

Although the task was simple enough (create a multi-file uploader with some
goals like type checking and resizing), I decided to do this rather thoroughly
(modular OOP with easy configuration) than some "quick-and-dirty" sloppy procedural code.

I think the resulting app might be a viable uploading solution for some people, even
though there are maaaany uploading apps on the Internet.

This also was kind of an educational project for me because I used famo.us for
the first time. I only did some tutorials for ~2 hours, so be gentle with me
if the code (`assets/app.js`) is not that great! ;)

If you have feedback, maybe about bugs or security issues, contact me at
[info@elias-kuiter.de](mailto:info@elias-kuiter.de).
