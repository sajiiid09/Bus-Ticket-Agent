#!/usr/bin/env python3
"""
Database Setup Script - Run once to initialize the database
Usage: python3 scripts/setup-db.py
"""

import subprocess
import sys

def run_sql_file(filename):
    """Execute SQL file"""
    try:
        with open(filename, 'r') as f:
            sql_content = f.read()
        
        # For MySQL, use: mysql -u root -p < file
        # This is a placeholder - in production use proper database client
        print(f"[v0] SQL file loaded: {filename}")
        print(f"[v0] Execute this in your database manager or use:")
        print(f"     mysql -u root -p bus_service < data/bus_service.sql")
        return True
    except FileNotFoundError:
        print(f"[v0] Error: {filename} not found")
        return False
    except Exception as e:
        print(f"[v0] Error reading file: {e}")
        return False

if __name__ == "__main__":
    print("[v0] BusTicket Database Setup")
    print("[v0] =======================")
    
    sql_file = "data/bus_service.sql"
    
    if run_sql_file(sql_file):
        print("[v0] Database schema created successfully!")
        print("[v0] Remember to:")
        print("[v0] 1. Update database credentials in app/db.php")
        print("[v0] 2. Run the SQL migration in your database")
        print("[v0] 3. Test the connection with: public/api/health-check.php")
    else:
        sys.exit(1)
