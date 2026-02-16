# Booking Engine API - High Avaibility & Concurrency Control

Questo è un sistema backend professionale per la gestione delle prenotazioni in tempo reale, focalizzato sull'integrità dei dati e sulla risoluzione dei conflitti in scenari ad altro traffico.

## Tech Stack
* **Framework:** Laravel 12
* **Database:** PostgreSQL (gestito con DBngin e TablePlus)
* **Testing:** PHPUnit (automatico) e Postman (manuale)
* **Environment:** Laravel Herd

## Architettura e Design Pattern
Il progetto segue una struttura studiata per garantire scalabilità e testabilità:
* **Service Layer Pattern:** La logica di business è isolata nello 'BookingService' per snellire i controller
* **Optimistic Locking:** Uso di un **Custom Trait** 'Lockable' per gestire il versionamento dei record e rispettare il DRY
* **Database Transaction:** Ogni operazione di scrittura è protetta da transazioni al fine rispettare l'atomicità

---

## Problemi riscontrati e come sono stati risolti

### Rilevamento delle sovrapposizioni (Overlap Detection)
**Problema:** Impedire che la stessa risorsa venga prenotata da due utenti diversi in intervalli di tempo incrociati anche parzialmente
**Soluzione:** Implementata una query basata sulla forma logica `(start1 < end2) AND (end1 > start2)`, approccio matematico nettamente superiore rispetto a `whereBetween`, perché copre ogni caso di collisione

### Precisione millesimale nei test
**Problema:** Durante i test automatizzati, le asserzioni fallivano casualmente a causa dei microsecondi generati da `now()`, che rendevano le date "diverse" per il database
**Soluzione:** Utilizzo di `Carbon::parse()` e normalizzazione dei secondi nel `BookingService` e nei test, garantendo una precisione al minuto costante tra PHP e PostgreSQL.

### Mass Assignment Protection
**Problema:** Errori di sicurezza durante l'inizializzazione del database tramite factory e seeder.
**Soluzione:** Configurazione rigorosa della proprietà `$fillable` nei modelli `Booking` e `Resource` per proteggere l'integrità dei dati senza bloccare le operazioni legittime del framework.

---

## Come avviare il progetto

1. **Clona la repo:**
`bash git clone [https://github.com/n0tR3tr0-code/booking-engine.git](https://github.com/n0tR3tr0-code/booking-engine.git)
   cd booking-engine`
2. **Installa le dipendenze:*** `composer install`
3. **Configura l'ambiente:** Crea il database `booking_engine` su PostgreSQL e copia il file `.env.example` in `.env` e configura i parametri `DB_*`
4. **Migrazioni e Seed:** Inserisci ed esegui questi due comandi nel terminale `php artisan migrate` `php artisan db:seed`
5. **Esegui il test:** Inserisci ed esegui nel terminale `php artisan test`

## Testing Coverage
Il progetto include una suite di test che copre:
* Creazione corretta di una prenotazione
* **Edge Case:** fallimento della prenotazione se l'orario si sovrappone ad una già esistente
* Validazione dei dati obbligatori
