# School Management System (Core Architecture Bypass)

This repository is a modified version of an open-source Laravel school management template. I used this project as a sandbox to test my backend debugging and database architecture skills.

### What I Did:
The original legacy code contained several critical errors in its 2025 migration files and database connection settings. My core contributions to getting this system live locally include:
1. **Migration Refactoring:** Identified and bypassed broken database migration files (specifically conflicting foreign keys in `jadwal_kerjas` and duplicate columns in `lokasi_presensi`).
2. **Database Configuration:** Resolved MySQL 8+ authentication plugin conflicts (`caching_sha2_password` vs `mysql_native_password`) to successfully seed the database.
3. **View Logic Bypassing:** Debugged and bypassed undefined variables in the Laravel Blade authentication views to restore dashboard access.

### Tech Stack:
- PHP / Laravel 11
- MySQL (Laragon)
- MVC Architecture

*Note: This repository is intended as a technical R&D showcase for backend debugging and is not meant for production deployment.*
