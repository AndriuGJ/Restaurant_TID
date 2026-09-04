# Migraciones y Modelos Laravel — Sistema de Restaurante

Código completo en **orden de creación**, siguiendo convenciones de Laravel:
- Modelos en `App\Models`, en singular PascalCase.
- `php artisan make:model NombreModelo -m` crea el modelo **y** la migración en un solo comando.
- Llaves foráneas con `foreignId()->constrained()`.
- `$fillable` explícito (más seguro que `$guarded = []`).
- `casts()` para booleanos, decimales y fechas.
- Relaciones Eloquent en ambos sentidos (`belongsTo` / `hasMany` / `morphTo` / `morphMany`).

---

## MÓDULO 1: CONFIGURACIÓN INICIAL Y BASE

### 1. ubigeos

```bash
php artisan make:model Ubigeo -m
```

**Migración** — `database/migrations/xxxx_xx_xx_000001_create_ubigeos_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubigeos', function (Blueprint $table) {
            $table->id();
            $table->string('department', 100);
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('code', 10)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubigeos');
    }
};
```

**Modelo** — `app/Models/Ubigeo.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'department',
        'province',
        'district',
        'code',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
```

---

### 2. timezones

```bash
php artisan make:model Timezone -m
```

**Migración** — `..._create_timezones_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('offset', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timezones');
    }
};
```

**Modelo** — `app/Models/Timezone.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'offset',
    ];
}
```

---

### 3. currencies

```bash
php artisan make:model Currency -m
```

**Migración** — `..._create_currencies_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('symbol', 10);
            $table->boolean('is_default')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
```

**Modelo** — `app/Models/Currency.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'symbol',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }
}
```

---

### 4. document_types

```bash
php artisan make:model DocumentType -m
```

**Migración** — `..._create_document_types_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('nomenclature', 20)->nullable();
            $table->integer('character_limit')->nullable();
            $table->enum('type', ['identification', 'invoice']);
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
```

**Modelo** — `app/Models/DocumentType.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'nomenclature',
        'character_limit',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function companyClients()
    {
        return $this->hasMany(CompanyClient::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
```

---

### 5. payment_methods

```bash
php artisan make:model PaymentMethod -m
```

**Migración** — `..._create_payment_methods_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->enum('type', ['cash', 'card', 'digital']);
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
```

**Modelo** — `app/Models/PaymentMethod.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
```

---

### 6. companies

```bash
php artisan make:model Company -m
```

**Migración** — `..._create_companies_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('commercial_name', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('commercial_address')->nullable();
            $table->string('ruc', 20);
            $table->string('social_reason', 255);
            $table->text('fiscal_address')->nullable();
            $table->foreignId('ubigeo_id')->nullable()->constrained('ubigeos')->nullOnDelete();
            $table->string('logo')->nullable();
            $table->string('sol_user', 50)->nullable();
            $table->string('sol_password', 100)->nullable();
            $table->string('digital_certificate_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
```

**Modelo** — `app/Models/Company.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'commercial_name',
        'phone',
        'commercial_address',
        'ruc',
        'social_reason',
        'fiscal_address',
        'ubigeo_id',
        'logo',
        'sol_user',
        'sol_password',
        'digital_certificate_path',
    ];

    protected $hidden = [
        'sol_password',
        'digital_certificate_path',
    ];

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class);
    }

    public function sunatConfigs()
    {
        return $this->hasMany(SunatConfig::class);
    }
}
```

---

### 7. sunat_configs

```bash
php artisan make:model SunatConfig -m
```

**Migración** — `..._create_sunat_configs_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sunat_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->integer('max_receipts');
            $table->integer('used_receipts')->default(0);
            $table->decimal('card_surcharge_percentage', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sunat_configs');
    }
};
```

**Modelo** — `app/Models/SunatConfig.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SunatConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'start_date',
        'end_date',
        'status',
        'max_receipts',
        'used_receipts',
        'card_surcharge_percentage',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'card_surcharge_percentage' => 'decimal:2',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
```

---

## MÓDULO 2: USUARIOS Y AUTENTICACIÓN

### 8. users

> Laravel ya trae `User` y su migración por defecto. En vez de crear otro modelo, **edita** `database/migrations/xxxx_xx_xx_000000_create_users_table.php` y `app/Models/User.php` para adaptarlos a este esquema (no ejecutes `make:model` de nuevo).

**Migración** — `database/migrations/xxxx_xx_xx_000000_create_users_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 20)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('cargo', 100)->nullable();
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

**Modelo** — `app/Models/User.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'dni',
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'cargo',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function cashRegisterSessionsOpened()
    {
        return $this->hasMany(CashRegisterSession::class, 'user_opening_id');
    }

    public function cashRegisterSessionsClosed()
    {
        return $this->hasMany(CashRegisterSession::class, 'user_closing_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
```

---

## MÓDULO 3: RESTAURANTE (SALONES, MESAS, CAJAS, DELIVERY)

### 9. halls

```bash
php artisan make:model Hall -m
```

**Migración** — `..._create_halls_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halls');
    }
};
```

**Modelo** — `app/Models/Hall.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
```

---

### 10. tables

> ⚠️ El modelo se llama `Table` por convención (singular de `tables`). Si en tu proyecto usas `Doctrine\DBAL\Schema\Table` para modificar columnas, importa esa clase con alias para evitar choques de nombre.

```bash
php artisan make:model Table -m
```

**Migración** — `..._create_tables_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls')->cascadeOnDelete();
            $table->string('name', 50);
            $table->enum('shape', ['square', 'round', 'rectangular'])->default('square');
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
```

**Modelo** — `app/Models/Table.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'hall_id',
        'name',
        'shape',
        'status',
    ];

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleTables()
    {
        return $this->hasMany(SaleTable::class);
    }
}
```

---

### 11. shifts

```bash
php artisan make:model Shift -m
```

**Migración** — `..._create_shifts_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
```

**Modelo** — `app/Models/Shift.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function cashRegisterSessions()
    {
        return $this->hasMany(CashRegisterSession::class);
    }
}
```

---

### 12. cash_registers

```bash
php artisan make:model CashRegister -m
```

**Migración** — `..._create_cash_registers_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
```

**Modelo** — `app/Models/CashRegister.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function cashRegisterSessions()
    {
        return $this->hasMany(CashRegisterSession::class);
    }
}
```

---

### 13. delivery_providers

```bash
php artisan make:model DeliveryProvider -m
```

**Migración** — `..._create_delivery_providers_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_providers');
    }
};
```

**Modelo** — `app/Models/DeliveryProvider.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryProvider extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'phone',
        'contact_person',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
```

---

### 14. cash_register_sessions

```bash
php artisan make:model CashRegisterSession -m
```

**Migración** — `..._create_cash_register_sessions_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained('cash_registers');
            $table->foreignId('shift_id')->constrained('shifts');
            $table->foreignId('user_opening_id')->constrained('users');
            $table->foreignId('user_closing_id')->nullable()->constrained('users');
            $table->decimal('opening_amount', 10, 2);
            $table->decimal('closing_amount', 10, 2)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_sessions');
    }
};
```

**Modelo** — `app/Models/CashRegisterSession.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id',
        'shift_id',
        'user_opening_id',
        'user_closing_id',
        'opening_amount',
        'closing_amount',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function userOpening()
    {
        return $this->belongsTo(User::class, 'user_opening_id');
    }

    public function userClosing()
    {
        return $this->belongsTo(User::class, 'user_closing_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
```

---

## MÓDULO 4: CLIENTES

### 15. customers

```bash
php artisan make:model Customer -m
```

**Migración** — `..._create_customers_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('reference_address')->nullable();
            $table->foreignId('document_type_id')->nullable()->constrained('document_types');
            $table->string('document_number', 50)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
```

**Modelo** — `app/Models/Customer.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'reference_address',
        'document_type_id',
        'document_number',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function sales()
    {
        return $this->morphMany(Sale::class, 'clientable');
    }
}
```

---

### 16. company_clients

```bash
php artisan make:model CompanyClient -m
```

**Migración** — `..._create_company_clients_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_clients', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 20);
            $table->string('social_reason', 255);
            $table->string('phone', 20)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->foreignId('document_type_id')->nullable()->constrained('document_types');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_clients');
    }
};
```

**Modelo** — `app/Models/CompanyClient.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'social_reason',
        'phone',
        'contact_person',
        'document_type_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function sales()
    {
        return $this->morphMany(Sale::class, 'clientable');
    }
}
```

---

## MÓDULO 5: INVENTARIO Y PRODUCTOS

### 17. purchase_categories

```bash
php artisan make:model PurchaseCategory -m
```

**Migración** — `..._create_purchase_categories_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_categories');
    }
};
```

**Modelo** — `app/Models/PurchaseCategory.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

---

### 18. product_categories

```bash
php artisan make:model ProductCategory -m
```

**Migración** — `..._create_product_categories_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
```

**Modelo** — `app/Models/ProductCategory.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

---

### 19. suppliers

```bash
php artisan make:model Supplier -m
```

**Migración** — `..._create_suppliers_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 20);
            $table->string('social_reason', 255);
            $table->string('phone', 20)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
```

**Modelo** — `app/Models/Supplier.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ruc',
        'social_reason',
        'phone',
        'contact_person',
        'email',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
```

---

### 20. products

```bash
php artisan make:model Product -m
```

**Migración** — `..._create_products_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('cost_price', 10, 2)->default(0.00);
            $table->decimal('sale_price', 10, 2)->default(0.00);
            $table->string('image_url')->nullable();
            $table->decimal('stock', 10, 2)->default(0.00);
            $table->string('unit_of_measure', 50)->default('unidad');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories');
            $table->foreignId('purchase_category_id')->nullable()->constrained('purchase_categories');
            $table->enum('type', ['dish', 'supply', 'combo'])->default('supply');
            $table->boolean('is_pos_item')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

**Modelo** — `app/Models/Product.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'sale_price',
        'image_url',
        'stock',
        'unit_of_measure',
        'product_category_id',
        'purchase_category_id',
        'type',
        'is_pos_item',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'decimal:2',
            'is_pos_item' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function purchaseCategory()
    {
        return $this->belongsTo(PurchaseCategory::class);
    }

    // Insumos que componen este plato (cuando type = 'dish')
    public function ingredients()
    {
        return $this->hasMany(ProductIngredient::class, 'dish_id');
    }

    // Platos en los que este producto se usa como insumo
    public function usedAsIngredientIn()
    {
        return $this->hasMany(ProductIngredient::class, 'ingredient_id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function kardexMovements()
    {
        return $this->hasMany(KardexMovement::class);
    }
}
```

---

### 21. product_ingredients

```bash
php artisan make:model ProductIngredient -m
```

**Migración** — `..._create_product_ingredients_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 10, 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};
```

**Modelo** — `app/Models/ProductIngredient.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'dish_id',
        'ingredient_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
        ];
    }

    public function dish()
    {
        return $this->belongsTo(Product::class, 'dish_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Product::class, 'ingredient_id');
    }
}
```

---

## MÓDULO 6: COMPRAS

### 22. purchases

```bash
php artisan make:model Purchase -m
```

**Migración** — `..._create_purchases_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('purchase_type', ['contado', 'credito'])->default('contado');
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->string('series', 20)->nullable();
            $table->string('number', 50);
            $table->date('purchase_date');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
```

**Modelo** — `app/Models/Purchase.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'purchase_type',
        'document_type_id',
        'series',
        'number',
        'purchase_date',
        'subtotal',
        'tax',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function kardexMovements()
    {
        return $this->morphMany(KardexMovement::class, 'related_document');
    }
}
```

---

### 23. purchase_details

```bash
php artisan make:model PurchaseDetail -m
```

**Migración** — `..._create_purchase_details_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
```

**Modelo** — `app/Models/PurchaseDetail.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
```

---

## MÓDULO 7: VENTAS (POS, DELIVERY, RÁPIDA)

### 24. sales

```bash
php artisan make:model Sale -m
```

**Migración** — `..._create_sales_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('cash_register_session_id')->nullable()->constrained('cash_register_sessions');
            $table->foreignId('table_id')->nullable()->constrained('tables');
            $table->integer('guests')->default(1);
            $table->enum('sale_type', ['pos', 'delivery', 'quick_sale'])->default('pos');
            $table->boolean('is_takeaway')->default(false);
            $table->foreignId('delivery_provider_id')->nullable()->constrained('delivery_providers');
            $table->string('delivery_person_name', 150)->nullable();
            // Relación polimórfica: Customer o CompanyClient
            $table->nullableMorphs('clientable');
            $table->foreignId('document_type_id')->nullable()->constrained('document_types');
            $table->string('series', 20)->nullable();
            $table->string('number', 50)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'preparing'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
```

**Modelo** — `app/Models/Sale.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cash_register_session_id',
        'table_id',
        'guests',
        'sale_type',
        'is_takeaway',
        'delivery_provider_id',
        'delivery_person_name',
        'clientable_id',
        'clientable_type',
        'document_type_id',
        'series',
        'number',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_takeaway' => 'boolean',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegisterSession()
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function deliveryProvider()
    {
        return $this->belongsTo(DeliveryProvider::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    // Relación polimórfica: puede ser Customer o CompanyClient
    public function clientable()
    {
        return $this->morphTo();
    }

    public function saleTables()
    {
        return $this->hasMany(SaleTable::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function kardexMovements()
    {
        return $this->morphMany(KardexMovement::class, 'related_document');
    }
}
```

---

### 25. sale_tables

```bash
php artisan make:model SaleTable -m
```

**Migración** — `..._create_sale_tables_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_tables');
    }
};
```

**Modelo** — `app/Models/SaleTable.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleTable extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'table_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
```

---

### 26. sale_details

```bash
php artisan make:model SaleDetail -m
```

**Migración** — `..._create_sale_details_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('notes')->nullable();
            $table->enum('kitchen_status', ['pending', 'preparing', 'completed'])->default('pending');
            $table->timestamp('prep_started_at')->nullable();
            $table->timestamp('prep_completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
```

**Modelo** — `app/Models/SaleDetail.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
        'kitchen_status',
        'prep_started_at',
        'prep_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'prep_started_at' => 'datetime',
            'prep_completed_at' => 'datetime',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
```

---

### 27. sale_payments

```bash
php artisan make:model SalePayment -m
```

**Migración** — `..._create_sale_payments_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
```

**Modelo** — `app/Models/SalePayment.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'payment_method_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
```

---

## MÓDULO 8: KARDEX (HISTORIAL POLIMÓRFICO)

### 28. kardex_movements

```bash
php artisan make:model KardexMovement -m
```

**Migración** — `..._create_kardex_movements_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardex_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->enum('movement_type', ['purchase', 'sale', 'adjustment']);
            $table->decimal('quantity_in', 10, 2)->default(0.00);
            $table->decimal('quantity_out', 10, 2)->default(0.00);
            $table->decimal('balance', 10, 2);
            // Relación polimórfica: Purchase o Sale
            $table->nullableMorphs('related_document');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kardex_movements');
    }
};
```

**Modelo** — `app/Models/KardexMovement.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity_in',
        'quantity_out',
        'balance',
        'related_document_id',
        'related_document_type',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in' => 'decimal:2',
            'quantity_out' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación polimórfica: puede ser Purchase o Sale
    public function relatedDocument()
    {
        return $this->morphTo();
    }
}
```

---

## Resumen de comandos en orden (copiar y pegar)

```bash
php artisan make:model Ubigeo -m
php artisan make:model Timezone -m
php artisan make:model Currency -m
php artisan make:model DocumentType -m
php artisan make:model PaymentMethod -m
php artisan make:model Company -m
php artisan make:model SunatConfig -m
# users: editar migración y modelo por defecto, no ejecutar make:model
php artisan make:model Hall -m
php artisan make:model Table -m
php artisan make:model Shift -m
php artisan make:model CashRegister -m
php artisan make:model DeliveryProvider -m
php artisan make:model CashRegisterSession -m
php artisan make:model Customer -m
php artisan make:model CompanyClient -m
php artisan make:model PurchaseCategory -m
php artisan make:model ProductCategory -m
php artisan make:model Supplier -m
php artisan make:model Product -m
php artisan make:model ProductIngredient -m
php artisan make:model Purchase -m
php artisan make:model PurchaseDetail -m
php artisan make:model Sale -m
php artisan make:model SaleTable -m
php artisan make:model SaleDetail -m
php artisan make:model SalePayment -m
php artisan make:model KardexMovement -m

php artisan migrate
```
