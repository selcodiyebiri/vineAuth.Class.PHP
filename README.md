Hakkında
=================

Geçenler ek bir projem için lazım olmuştu. Yaklaşık bir buçuk saatlik bir çalışmanın sonucunda PHP tabanında iki kupa nescafe ve
üç adet milka sütlü çikolata eşliğinde bu sınıfı ortaya çıkarmış bulunmaktayım. 


Gereksinimler
=================

Sunucu cURL kütüphanesi bulunmalıdır ve PHP sürümü en az PHP 5.x olmalıdır.


Kullanımı
=================

Kullanımını elimden geldiğince basit tutmaya çalıştım ki basitte oldu yani. İlk olarak vine login kullanımı

```php
try
{
    $vine = new vineAuth;
    $login = $vine->login('EMAIL', 'PASSWORD');
    print_r($login);

} catch ( Exception $e )
{
    echo $e->getMessage();
}
```

Herhangi bir keyin profiline ulaşma.

```php
try
{
    $vine = new vineAuth;
    $me = $vine->me(KEY);
    print_r($me);

} catch ( Exception $e )
{
    echo $e->getMessage();
}
```

Elinizdeki vine session ids ile takipçi gönderme ve takipçiyi geri çekme kullanımı

```php
try
{
    $keys = ['KEY 1', 'KEY 2', 'KEY 3', ...];
    $vine = new vineAuth;
    $vine->setKeys($keys);
    $follow = $vine->follow(ID);
    //$unfollow = $vine->follow(ID, TRUE);
    print_r($follow);

} catch ( Exception $e )
{
    echo $e->getMessage();
}
```

Vine session ids ile beğeni gönderme ve beğeni vazgeçme kullanımı

```php
try
{
    $keys = ['KEY 1', 'KEY 2', 'KEY 3', ...];
    $vine = new vineAuth;
    $vine->setKeys($keys);
    $like = $vine->like(ID);
    //$unlike = $vine->like(ID, TRUE);
    print_r($like);

} catch ( Exception $e )
{
    echo $e->getMessage();
}
```

Vine session ids ile revine gönderme

```php
try
{
    $keys = ['KEY 1', 'KEY 2', 'KEY 3', ...];
    $vine = new vineAuth;
    $vine->setKeys($keys);
    $revine = $vine->revine(ID);
    print_r($revine);

} catch ( Exception $e )
{
    echo $e->getMessage();
}
```
