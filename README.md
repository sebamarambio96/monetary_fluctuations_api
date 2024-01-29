# Documentación
Bienvenido a este pequeño proyecto de Laravel en el cual accederemos a la API externa https://mindicador.cl/. Esta API recibe como parámetros el tipo de moneda y el año, devolviendo la fluctuación de la divisa seleccionada en pesos chilenos (CLP).
# Instalación:
Al ser un proyecto pequeño y para facilitar la revisión por parte de un tercero, he decidido utilizar una base de datos autocontenida en el proyecto. Por lo tanto, para instalarlo, solo debemos clonar el repositorio e instalar las dependencias, como detallo a continuación:
```
git clone https://github.com/sebamarambio96/monetary_fluctuations_api.git
cd ./monetary_fluctuations_api
composer install
```
Como paso final debemos renombrar el archivo ```.env.example``` a ```.env```.
# ¡Comenzamos!
Para empezar, solo necesitamos iniciar el servidor de forma local ejecutando este comando desde la carpeta raiz:
```
php artisan serve
```
# Base de datos:
Como comenté anteriormente, me pareció una gran idea crear este proyecto con una base de datos autocontenida para facilitar su revisión, este archivo esta presente en la ruta ```/data/api-dolar.sqlite```. Por lo tanto, utilicé [SQLite](https://www.sqlite.org/index.html). Esta base de datos me permite compartir mi proyecto de manera ágil y además proporciona una velocidad de respuesta óptima al interactuar con ella en el frontend.

A continuación detallaré la estructura de la base de datos:

- Currencies: Esta tabla está dedicada a almacenar las divisas disponibles para consultar en la API externa. Si el estado (status) se cambia a 1, el sistema entenderá que es una divisa de interés. En la próxima ejecución del comando ```app:savedata```, se incluirá como una moneda de interés y se guardará su información.

| currencies | Detalles |
| ------ | ------ |
| id | INTEGER / PK / UNIQUE / NOT NULL|
| name | TEXT / NOT NULL |
| status | INTEGER(1) / NOT NULL |

- interest_years: Al igual que la tabla anterior, esta guarda los años de interés, en este caso, 2023 y 2024, para que al realizar la consulta, pida y almacene su información.

| interest_years | Detalles |
| ------ | ------ |
| year | TEXT / PK / UNIQUE / NOT NULL |
| status | INTEGER(1) / NOT NULL |

- monetary_fluctuations: Esta tabla es la que se consulta desde el front-end para obtener los registros de la fluctuación de las divisas guardadas. Para identificar cada registro con su divisa correspondiente, se creó una llave foránea llamada ```id_currencies``` la cual apunta a la tabla ```currencies```.

| monetary_fluctuations | Detalles |
| ------ | ------ |
| id | INTGER / PK / UNIQUE / NOT NULL |
| date | TEXT / NOT NULL |
| value_clp | REAL / NOT NULL |
| id_currencies | INTEGER / FK |

# Modelos:
Para crear los modelos, utilicé la dependencia [Reliese/Laravel](https://github.com/reliese/laravel) la cual me permite transcribir la estructura de una base de datos existente a la clase "Model" de Laravel, permitiéndome crear modelos precisos de manera rápida y eficiente.

# Servicios:
En esta ocasión, solo construí un servicio que se encarga principalmente de obtener los años y divisas de interés. Posteriormente, proporciona métodos para obtener y manipular estos datos.

1. **`__construct():`**
   - *Descripción:* Constructor de la clase. Se encarga de inicializar las propiedades `activeYears` y `activeCurrencies` utilizando los métodos `getActiveYears()` y `getActiveCurrencies()`.

2. **`getAllInterestData():`**
   - *Descripción:* Obtiene datos de interés para todas las divisas y años activos en la base de datos. Itera sobre las divisas activas y los años activos, recuperando sus datos desde la API.

3. **`saveMonetaryFluctuationBBDD($currencyData):`**
   - *Descripción:* Guarda datos de fluctuación monetaria en la base de datos. Si el dato para la fecha y divisa no existe este se creará, en el caso de que exista y tenga un valor distinto se actualizará.

4. **`fetchCurrencyData($year, $currencyName):`**
   - *Descripción:* Método estático que realiza una solicitud HTTP GET a una API externa para obtener datos de fluctuación de una divisa específica en un año determinado.

5. **`getActiveYears():`**
   - *Descripción:* Método estático que devuelve un conjunto de años activos.

6. **`getActiveCurrencies():`**
   - *Descripción:* Método estático que devuelve un conjunto de divisas activas.

# Controladores y Router:
El controlador de mayor interés es ```DataCollectorController```, el cual se construye desde un principio con las reglas y mensajes de validación. Estos mensajes han sido configurados en español, ya que, con la excepción de los errores fatales, podrán ser utilizados en el front-end para renderizar alertas en caso de algún error de validación.

El metodo llamado ```getCurrencyValues(Request $request, $currencyName)``` es consultado a través de este endpoint:
```
{url_base}/get-currency-values/{currencyName}/?start_date={startDate}&end_date={endDate}
```
Que como podemos ver recibe a través de la URL los siguientes parámetros:

| Parámetros | Detalles |
| ------ | ------ |
| currencyName | Nombre válido de la divisa | 
| start_date | Fecha de YYYY-MM-D |
| end_date | Fecha final YYYY-MM-D |

Entonces, un ejemplo podría ser:
```
http://127.0.0.1:8000/api/get-currency-values/dolar/?start_date=2024-01-01&end_date=2024-01-31
```
Con esto, podremos obtener los datos correspondientes a la moneda que buscamos, en la fecha que solicitemos. En caso de no tener datos guardados en la base de datos, retornará un array vacío.
# Tareas programadas:
He dejado una tarea programada, la cual sirve para revisar cuáles son los años y divisas de interés y ejecutar los mecanismos necesarios para obtenerla de la API externa y guardarla de manera ordenada en la base de datos. A continuación, dejaré una lista con los comandos asociados a esta tarea:

-   Para ver lista de tareas programadas:
```
php artisan schedule:list
```

-   Para ejecutar SaveData de manera on-demand:
```
php artisan app:savedata
```
- En caso de desplegarlo en un sistema Linux, se debe configurar el cron con el siguiente código:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```



------
Para finalizar, solo espero que este proyecto, aunque simple, les haya parecido interesante.
