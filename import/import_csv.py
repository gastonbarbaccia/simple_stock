import pandas as pd
import mysql.connector
from datetime import datetime

# Configurar conexión
conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',
    database='simple_stock'
)
cursor = conn.cursor()

# Leer el CSV
df = pd.read_csv('datos.csv')

# Insertar categorías únicas
categorias = df[['nombre_categoria', 'descripcion_categoria']].drop_duplicates()

categoria_ids = {}
for _, row in categorias.iterrows():
    nombre = row['nombre_categoria']
    descripcion = row['descripcion_categoria']
    
    # Verificamos si ya existe
    cursor.execute("SELECT id_categoria FROM categorias WHERE nombre_categoria = %s", (nombre,))
    result = cursor.fetchone()
    
    if result:
        categoria_ids[nombre] = result[0]
    else:
        cursor.execute("""
            INSERT INTO categorias (nombre_categoria, descripcion_categoria, date_added)
            VALUES (%s, %s, %s)
        """, (nombre, descripcion, datetime.now()))
        conn.commit()
        categoria_ids[nombre] = cursor.lastrowid

# Insertar productos
for _, row in df.iterrows():
    codigo = row['codigo_producto']
    nombre = row['nombre_producto']
    precio = row['precio_producto']
    stock = row['stock']
    nombre_categoria = row['nombre_categoria']
    
    id_categoria = categoria_ids[nombre_categoria]
    
    cursor.execute("""
        INSERT INTO products (codigo_producto, nombre_producto, date_added, precio_producto, stock, id_categoria)
        VALUES (%s, %s, %s, %s, %s, %s)
    """, (codigo, nombre, datetime.now(), precio, stock, id_categoria))
    conn.commit()

cursor.close()
conn.close()

print("Importación completada.")
