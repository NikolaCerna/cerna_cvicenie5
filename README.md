# Notes REST API – Laravel

## Získanie všetkých poznámok

**URI**

```
http://localhost:8000/api/notes
```

![GET notes](images/get_notes_index.png)

---

## Získanie jednej poznámky

**URI**

```
http://localhost:8000/api/notes/{id}
```

Príklad:

```
http://localhost:8000/api/notes/2
```

![GET note](images/get_note_show.png)

---

## Vytvorenie novej poznámky

**URI**

```
http://localhost:8000/api/notes
```

![POST note](images/post_note_store.png)

---

## Aktualizácia poznámky

**URI**

```
http://localhost:8000/api/notes/{id}
```

Príklad:

```
http://localhost:8000/api/notes/2
```

![PUT note](images/patch_note_update.png)

---

## Odstránenie poznámky (soft delete)

**URI**

```
http://localhost:8000/api/notes/{id}
```

Príklad:

```
http://localhost:8000/api/notes/2
```

![DELETE note](images/destroy_note_delete.png)

---

# Ďalšie endpointy

## Vyhľadávanie poznámok

Vyhľadáva poznámky podľa textu v **title** alebo **body**.

**URI**

```
http://localhost:8000/api/notes-actions/search?q=lara
```

![Search](images/get_note_search.png)

---

## Štatistika podľa statusu

**URI**

```
http://localhost:8000/api/notes/stats/status
```

![Stats](images/get_note_stats.png)

---

## Archivovanie starých draftov

**URI**

```
http://localhost:8000/api/notes/actions/archive-old-drafts
```

![Archive drafts](images/patch_note_archiveolddrafts.png)

---

## Poznámky používateľa s kategóriami

**URI**

```
http://localhost:8000/api/users/{user}/notes
```

Príklad:

```
http://localhost:8000/api/users/2/notes
```

![User notes](images/get_note_usernoteswithcategories.png)

---

## pinned notes

Endpoint vracia všetky pripnuté poznámky (`is_pinned = true`).

**URI**

```
http://localhost:8000/api/notes/actions/pinned
```

![Pinned notes](images/get_note_ispinned.png)

---

## Publikovanie poznámky

**URI**

```
http://localhost:8000/api/notes/{id}/publish
```

Príklad:

```
http://localhost:8000/api/notes/2/publish
```
![Published notes](images/patch_note_publish.png)
---

## Pripnutie poznámky

**URI**

```
http://localhost:8000/api/notes/{id}/pin
```

Príklad:

```
http://localhost:8000/api/notes/2/pin
```
![Pin note](images/patch_note_pin.png)
---

## Odopnutie poznámky

**URI**

```
http://localhost:8000/api/notes/{id}/unpin
```

Príklad:

```
http://localhost:8000/api/notes/2/unpin
```
![Unin note](images/patch_note_unpin.png)
---

# Categories REST API – Laravel

## Získanie všetkých kategórií

**URI**

```
http://localhost:8000/api/categories
```

![GET categories](images/get_categories_index.png)

---

## Získanie jednej kategórie

**URI**

```
http://localhost:8000/api/categories/{id}
```

Príklad:

```
http://localhost:8000/api/categories/2
```

![GET category](images/get_categories_show.png)

---

## Vytvorenie novej kategórie

**URI**

```
http://localhost:8000/api/categories
```


![POST category](images/post_categories_store.png)

---

## Aktualizácia kategórie

**URI**

```
http://localhost:8000/api/categories/{id}
```

Príklad:

```
http://localhost:8000/api/categories/2
```

![PUT category](images/patch_categories_update.png)

---

## Odstránenie kategórie

**URI**

```
http://localhost:8000/api/categories/{id}
```

Príklad:

```
http://localhost:8000/api/categories/2
```

![DELETE category](images/delete_categories_destroy.png)

---


# Testovanie API

API bolo testované pomocou nástroja **Postman**.
Každý endpoint bol overený pomocou HTTP requestov a odpovedí vo formáte **JSON**.

---

# Autor

Nikola Černá
