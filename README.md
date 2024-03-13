# symfony-fortune

This project is a simple application built with Symfony. The goal is to test and demonstrate different ways of working with Doctrine ORM in Symfony. This example is inspired by [symfonycasts](https://symfonycasts.com/screencast/doctrine-queries/dql).

## Features

* ✅ Symfony 7
* ✅ Doctrine ORM
* ✅ DataFixtures + zenstruck/foundry
* ✅ Tailwind
* ✅ Stimulus
* ✅ PHPStan
* ✅ Laravel Pint (PHP Coding Standards Fixer)
* ✅ GitHub Actions
* 🚫 Tests

## Installation

Install dependencies using Composer

```
composer install
```

Create your .env file from example

```
cp .env.example .env
```
## Examples

### Doctrine DQL

```php
/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @return Category[] 
     */
    public function findAllOrdered(): array
    {
        $dql = 'SELECT c FROM App\Entity\Category as c ORDER BY c.name';
        $query = $this->getEntityManager()->createQuery($dql);
        
        return $query->getResult();
    }
}
```

### QueryBuilder

```php
/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @return Category[] 
     */
    public function findAllOrdered(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->addOrderBy('c.name', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
}
```

### N+1 Problem
N+1 queries are a performance problem in which the application makes database queries in a loop, instead of making a single query.

```php
/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @return Category[] 
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('fc')
            ->leftJoin('c.fortuneCookies', 'fc')
            ->andWhere('c.name LIKE :searchTerm OR c.iconKey LIKE :searchTerm OR fc.fortune LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$term.'%')
            ->addOrderBy('c.name', Criteria::DESC)
            ->getQuery()
            ->getResult();
    }
}
```

### SELECTing into a New DTO Object

```php
/**
 * @extends ServiceEntityRepository<FortuneCookie>
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function countNumberPrintedByCategory(Category $category): CategoryFortuneStats
    {
        $qb = $this->createQueryBuilder('fc');
    
        $qb
            ->select(sprintf(
                'NEW %s(
                    SUM(fc.numberPrinted),
                    AVG(fc.numberPrinted),
                    c.name
                )',
                CategoryFortuneStats::class,
            ))
            ->join('fc.category', 'c')
            ->andWhere('fc.category = :category')
            ->setParameter('category', $category);
    
        return $qb->getQuery()->getSingleResult();
    }
}
```

### Raw SQL Queries

```php
/**
 * @extends ServiceEntityRepository<FortuneCookie>
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function countNumberPrintedByCategory(Category $category): CategoryFortuneStats
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT SUM(fc.number_printed) as fortunesPrinted, AVG(fc.number_printed) as fortunesAverage, c.name as categoryName FROM fortune_cookie fc INNER JOIN category c ON fc.category_id = c.id WHERE fc.category_id = :category';
        $statement = $connection->prepare($sql);
        $result = $statement->executeQuery([
            'category' => $category->getId(),
        ]);

        $data = $result->fetchAssociative();

        return new CategoryFortuneStats(
            (int) $data['fortunesPrinted'],
            (int) $data['fortunesAverage'],
            $data['categoryName'],
        );
    }
}
```

### Reusing Queries in the Query Builder

Separate logic to simple class.

```php
final class CategoryJoinAndSelectFortuneCookie extends AbstractQuery
{
    public function build(): QueryBuilder
    {
        return $this->qb
            ->addSelect('fc')
            ->leftJoin('c.fortuneCookies', 'fc');
    }
}

final class CategoryOrderByName extends AbstractQuery
{
    private string $order = 'ASC';

    public function addOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function build(): QueryBuilder
    {
        return $this->qb
            ->addOrderBy('c.name', $this->order);
    }
}
```

Than we can reusing these classes in repository

```php
/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @return Category[]
     */
    public function findAllOrdered(): array
    {
        $qb = $this->createQueryBuilder('c');

        CategoryGroupByCategoryAndCountFortuneCookies::create($qb)
            ->build();
        CategoryOrderByName::create($qb)
            ->addOrder('DESC')
            ->build();

        return $qb->getQuery()->getResult();
    }
}
```

### Criteria: Filter Relation Collections

The best solution in not to use criteria inside entity class. 

```php
final class FortuneCookiesStillInProduction
{
    public static function create(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('discontinued', false));
    }
}

class Category
{
    /**
     * @return Collection<int, FortuneCookie>
     */
    public function getFortuneCookiesStillInProduction(): Collection
    {
        return $this->fortuneCookies->matching(FortuneCookiesStillInProduction::create());
    }
}
```

### Filters: Automatically Modify Queries

Doctrine features a filter system that allows the developer to add SQL to the conditional clauses of queries

```php
class FortuneCookieDiscontinuedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if ($targetEntity->name !== FortuneCookie::class) {
            return '';
        }

        return sprintf('%s.discontinued = %s', $targetTableAlias, (int) $this->getParameter('discontinued'));
    }
}
```

```yaml
doctrine:
  # ...
  orm:
    # ...
    filters:
      fortune_cookie_discontinued:
        class: App\Doctrine\Filter\FortuneCookieDiscontinuedFilter
        enabled: true
        parameters:
          discontinued: false
```

### Using RAND() or Other Non-Supported Functions

```
composer require beberlei/doctrineextensions
```

```yaml
doctrine:
  # ...
  orm:
    # ...
    dql:
      numeric_functions:
        rand: DoctrineExtensions\Query\Mysql\Rand
```

### Using GROUP BY to Fetch & Count in 1 Query

Maybe this is not the best solution. We create temporary variable which is not store in database.

```php
class Category
{
    // temporary
    private int $fortuneCookiesTotal = 0;
    
    public function getFortuneCookiesTotal(): int
    {
        return $this->fortuneCookiesTotal;
    }

    public function setFortuneCookiesTotal(int $fortuneCookiesTotal): self
    {
        $this->fortuneCookiesTotal = $fortuneCookiesTotal;

        return $this;
    }
}
```

When we fetch data in 1 query, we will set our temporary variable.

```php
/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @return Category[]
     */
    public function findAllOrdered(): array
    {
        $qb = $this->createQueryBuilder('c');

        CategoryGroupByCategoryAndCountFortuneCookies::create($qb)
            ->build();
        CategoryOrderByName::create($qb)
            ->addOrder('DESC')
            ->build();

        return $this->getCategoryWithFortuneCookiesTotal($qb->getQuery()->getResult());
    }
    
    /**
     * @return Category[]
     */
    public function search(string $term): array
    {
        $qb = $this->createQueryBuilder('c');

        $terms = explode(' ', $term);
        CategoryGroupByCategoryAndCountFortuneCookies::create($qb)
            ->build();
        CategoryOrderByName::create($qb)
            ->addOrder('DESC')
            ->build();

        $qb->andWhere('c.name LIKE :term OR c.name IN (:terms) OR c.iconKey LIKE :term OR fc.fortune LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('terms', $terms);

        return $this->getCategoryWithFortuneCookiesTotal($qb->getQuery()->getResult()); // @phpstan-ignore-line
    }
    
    /**
     * @param  array<int, array{ category: Category, fortuneCookiesTotal: int }>  $results
     * @return Category[]
     */
    private function getCategoryWithFortuneCookiesTotal(array $results): array
    {
        /** @var array<int, Category> $categories */
        $categories = [];

        foreach ($results as $result) {
            $categories[] = $result['category']->setFortuneCookiesTotal($result['fortuneCookiesTotal']);
        }

        return $categories;
    }
}
```
