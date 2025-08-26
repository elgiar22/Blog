<?php

/*
erd -->done
query , connection

crud
read all=> index -->done
read one => view -->done

pagination 


add (image require)
update (optional)
delete 

authantication (register , login , logout)
authorization




localization(english , arabic) 

 0-file ar, file en ok

1-link lang -> file store press lang ->session() ok 

2-redirect the same place -> server ok

3-data of lang




*/




/*

limit $limit offset $offset

$limit 2;

offset = (page - 1)*limit 

page  limit  offset
1       2       0
2       2       2
3       2       4




*/



/*
📦 Project: Blog Website
👨‍💻 Developer: Ahmed El-Gayar
📅 Last Updated: 2025-07-04
🧪 Type: Manual Testing Coverage
📄 Format: Feature Check List

--------------------------------------------------
🔗 DATABASE & STRUCTURE
--------------------------------------------------
☑️ Database connected successfully (conn.php)
☑️ Table: users (id, name, email, password)
☑️ Table: posts (id, title, body, image, user_id, created_at)

--------------------------------------------------
📝 BLOG FUNCTIONALITY (CRUD)
--------------------------------------------------
☑️ Create Post
   ☑️ Form has validation (title, body, image)
   ☑️ Rejects empty or invalid image
☑️ Read Posts (index.php)
   ☑️ Posts display properly
☑️ Read Single Post (post.php?id=)
   ☑️ Post appears with full info
☑️ Update Post
   ☑️ Existing values loaded into form
   ☑️ Image optional during update
☑️ Delete Post
   ☑️ Post is removed from database
   ☑️ Image file is deleted (if applicable)

--------------------------------------------------
📑 PAGINATION
--------------------------------------------------
☑️ Pagination implemented on index.php
☑️ Fixed limit: 2 per page
☑️ offset = (page - 1) * limit
☑️ Navigation works (next, previous)

--------------------------------------------------
🔐 AUTHENTICATION
--------------------------------------------------
☑️ Register
   ☑️ Validation for email, password length
   ☑️ Prevent duplicate emails
☑️ Login
   ☑️ password_verify used
   ☑️ Stores user_id in $_SESSION
☑️ Logout
   ☑️ session_unset + session_destroy

--------------------------------------------------
🛂 AUTHORIZATION
--------------------------------------------------
☑️ Cannot access add/edit/delete pages without login
☑️ Cannot manipulate others’ posts (if ownership checked)

--------------------------------------------------
🌐 LOCALIZATION (LANGUAGE SUPPORT)
--------------------------------------------------
☑️ English (default)
☑️ Arabic supported
☑️ lang/en.php and lang/ar.php exist
☑️ Session-based language switch
☑️ All labels dynamic (not hardcoded)

--------------------------------------------------
🧼 VALIDATION & SECURITY
--------------------------------------------------
☑️ All form inputs validated server-side
☑️ htmlspecialchars used to prevent XSS
☑️ Password hashed using password_hash
☑️ SQL Injection mitigated via escaping or validation
☑️ Uploaded files restricted by type (.jpg, .png)

--------------------------------------------------
🧪 SECURITY TESTS (Manual)
--------------------------------------------------
☑️ SQL Injection tested (login, search) → blocked
☑️ XSS tested (title/body) → encoded
☑️ Direct access to private routes → redirected
☑️ Session hijacking/resume tested

--------------------------------------------------

*/

