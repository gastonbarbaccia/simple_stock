import pandas as pd
import mysql.connector
from datetime import datetime

# Configurar conexión
conn = mysql.connector.connect(
    host='srv1451.hstgr.io',
    user='u666073011_stock',
    password='IPa=dV9[f',
    database='u666073011_stock'
)
cursor = conn.cursor()

# Leer el CSV
df = pd.read_csv('datos.csv')

# Asegurar categoría 'General'
cursor.execute("SELECT id_categoria FROM categorias WHERE nombre_categoria = %s", ('General',))
result = cursor.fetchone()

if result:
    id_general = result[0]
else:
    cursor.execute("""
        INSERT INTO categorias (nombre_categoria, descripcion_categoria, date_added)
        VALUES (%s, %s, %s)
    """, ('General', 'Categoría por defecto', datetime.now()))
    conn.commit()
    id_general = cursor.lastrowid

# Insertar categorías únicas
categorias = df[['nombre_categoria', 'descripcion_categoria']].drop_duplicates()
categoria_ids = {'General': id_general}

for _, row in categorias.iterrows():
    nombre = row['nombre_categoria']
    descripcion = row['descripcion_categoria']

    if pd.isna(nombre):
        continue

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

# Función para limpiar y convertir precios
def limpiar_precio(valor):
    if pd.isna(valor):
        return 0.0
    valor = str(valor).replace('$', '').replace('.', '').replace(',', '.')
    try:
        return float(valor)
    except ValueError:
        print(f"⚠️ Valor inválido para precio: {valor}")
        return 0.0

# Insertar productos
for _, row in df.iterrows():
    codigo = row['codigo_producto']
    nombre = row['nombre_producto']
    precio = limpiar_precio(row['precio_producto_cons_final'])
    precio2 = limpiar_precio(row['precio_producto_reventa'])
    stock = row['stock']
    nombre_categoria = row['nombre_categoria']

    # Usar 'General' si la categoría no está en el diccionario
    id_categoria = categoria_ids.get(nombre_categoria, id_general)

    try:
        cursor.execute("""
            INSERT INTO products (codigo_producto, nombre_producto, date_added, precio_producto_cons_final, precio_producto_reventa , stock, id_categoria)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
        """, (codigo, nombre, datetime.now(), precio, precio2, stock, id_categoria))
        conn.commit()
    except Exception as e:
        print("\n❌ Error al ejecutar INSERT en products")
        print("Query: INSERT INTO products (...) VALUES (%s, %s, %s, %s, %s, %s, %s)")
        print("Valores:", (codigo, nombre, datetime.now(), precio, precio2, stock, id_categoria))
        print("Error:", e)

cursor.close()
conn.close()

print("✅ Importación completada.")
