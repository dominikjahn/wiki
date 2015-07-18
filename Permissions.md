The following permissions are included:

`ALTER_USERPERMISSIONS` allows a user to manage user permissions

`CREATE_PAGES` allows the user to create new pages
`CREATE_USERS` allows the user to create new users
`CREATE_GROUPS` allows the user to create new groups

`DELETE_PAGES` allows the user to delete pages
`DELETE_USERS` allows the user to delete users
`DELETE_GROUPS` allows the user to delete groups

# Special permissions

`SCRIPTING` allows the user to create and edit pages with scripts.

---

Give this permission with care! A script can contain *ANY* PHP-code that can run in the server environment. This also means that the user has full access to the current database connection. He could then, for example, give himself the `ADMIN` permission (see below) or install backdoors by altering the source code of your installation. At present time there is nothing that would stop a user from doing such evil things - though there are ideas of how to prevent those.

---

`ADMIN` allows the user to do all the above things. It can also delete pages which are protected by their owner. However, it cannot view the content of those pages. Also, this permission cannot be revoked from other users. It requires that the user itself revokes it, and then another `ADMIN` has to confirm that by revoking it too. A user with this permission cannot be deleted!