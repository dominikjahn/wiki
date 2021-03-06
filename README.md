### For contributors

**Current milestone:** Pre 0.1 Release Candidate

Please checkout branch **0.1 RC** for contribution to next milestone.

**No commits without an issue!**

Always reference the issue in the commit message, e.g. "#11 Fixed bug in page rendering".

**Don't assign milestones to any issues!**

# What is this?

This repository is for a wiki software. It is in no way meant to be a competition to MediaWiki or any other wiki software out there. Instead, it is specifically for private use or internal company use. Of course you can make a public wiki website with it, but you might find that it lacks some features for it.

Pages can be created and made visible only to you or a group of people (e.g. you could create a group `sales` and let only employees in that group see the content of a page). Also, editing of pages can be restricted: a page may be public, but only users in the group `admin` can edit it. Or you can make a page visible only to registered users, but only **you** (the owner of the page) can edit it.

## Features

* Multiple users (users can either sign up themselves or a permitted user can create them)
* Users can be assigned to groups
* Individual pages can be
	* Public
	* Protected (only visible to signed in users)
	* Private (only visible to their owner)
	* Group private (only visible to users in a specific group)
* Pages can be assigned to categories
* Pages are stored in a local cache, so your website will still be available even when you can't connect to it
* Editing of pages can be restricted to everyone, registered users only, their owner only or users in their group only - for each individual page
* Permitted users can create and edit pages with PHP scripts

# Installation

* Create a directory, e.g. `/var/www/wiki.domain.com`
* Switch to that directory
* Clone this repository, e.g. `git clone https://github.com/dominikjahn/wiki.git .` (pay close attention to the `.` at the end: it means that all files will be copied into the current directory, and not a subdirectory `wiki/`)
* Replace `%PREFIX%` in `wiki/etc/Database Structure.sql` and `wiki/etc/Database Data.sql` and import them to your database
* Copy `wiki/Core/Configuration.example.php` to `wiki/Core/Configuration.php` and update your database information
* Create a virtual host file which points to `/var/www/wiki.domain.com/public_html`
* Go to your website, e.g. `http://wiki.domain.com`
* You can sign in using `admin` and `admin`
* **I highly recommend that you change that password immediately!** To do that, simply click on your login name on the bottom left of the Wiki website.