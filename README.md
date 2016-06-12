# Overnight
An overnightly simple PHP-mysql query builder based on PDO.

### Installation
#### Composer
```
composer require offworks/overnight
```
#### Git clone
```
git clone https://github.com/offworks/overnight
```
then, require the autoload from the location
```
require_once __DIR__.'/path/to/overnight/src/autoload.php';
```

### Create connection
```
$overnight = \Overnight\Connection::create('localhost', 'user', 'password', 'dbname');
```

### Querying
#### Simple query
Get an array of records
```
$users = $overnight->table('user')
        ->where('created_at > ?', array(date('Y-m-d H:i:s'))
        ->execute()->all();
```
Get an associated key value single (first) record
```
$user = $overnight->from('user')
        ->select('name, email, birthdate')
        ->where('user_id = ?', array($userId))
        ->execute()->first();
```
*p/s : from() is an alias of table()*

Multiple wheres
```
$tickets = $overnight->from('ticket')
           ->where('available = ?', array(1))
           ->where('expiry_date < ?', '2020-10-10')
           ->andWhere('seat_type = ?', array('single'))
           ->orWhere('master_ticket = ?', array(1))
           ->execute();

// will produce something like
SELECT * FROM ticket WHERE available = 1 AND expiry_date < '2020-10-10' AND seat_type = 'single'
```

#### Joining
Inner Join
```
$books = $overnight->from('book')
         ->innerJoin('author', 'author.author_id = book.author_id')
         ->execute()->all();
```
Left Join
```
$users = $overnight->from('user')
         ->leftJoin('membership', 'membership.user_id = user.user_id')
         ->execute()->all();
```
Prepared join
```
$users = $overnight->from('book')
        ->innerJoin('author', 'author.author_id = book.author_id AND author.is_alive = ?', array(1))
        ->execute()->all();
```

#### Ordering
```
$students = $overnight->from('student')
            ->where('is_alive = ?', array(1))
            ->orderBy('last_seen DESC')
            ->execute()->all();
```

#### Get Raw Sql
```
$query = $overnight->from('news')
        ->innerJoin('editor', 'news.editor_id = editor.editor_id')
        ->where('DATE(published_at) = ?', array(date('Y-m-d H:i:s')))
        ->orderBy('published_at DESC')
        ->sql();
```

### Inserting
```
$overnight->insert('user')->values(array(
    'name' => 'James',
    'like' => 'Foo cake',
    'birthdate' => '1977-05-09'
  ))->execute();
```
or
```
$overnight->insert('user', array(
    'name' => 'James',
    'like' => 'Foo cake',
    'birthdate' => '1977-05-09'
  ))->execute();
```

#### Last insert id
```
$userId = $overnight->lastInsertId();
```

### Updating
```
$overnight->update('book')->where('book_id = ?', array($bookId))
          ->set(array('title' => 'the lost marble - first edition'))
          ->execute();
```

### Deleting
```
$overnight->delete('author')
          ->where('author_id = ?', array($authorId))
          ->execute();
```
