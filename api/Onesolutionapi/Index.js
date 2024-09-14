const express = require('express')
const mysql = require('mysql')
const myconn = require('express-myconnection')


const app = express()

app.set('port', process.env.PORT || 3000)
const dbOptions = {
    host: process.env.DB_HOST || "localhost",
    port: process.env.DB_PORT || "3306",
    user: process.env.DB_USER || "root",
    password: process.env.DB_PASSWORD || "",
    database: process.env.DB_NAME || "admsystemct"
}

// Middlewares
app.use(myconn(mysql,dbOptions,'single'))
app.use(express.json())

//rutas
app.use(require('./rutas/Usuarios'))
app.use(require('./rutas/Parametros'))
app.use(require('./rutas/Roles'))
app.use(require('./rutas/Permisos'))
app.use(require('./rutas/Contraseña'))
app.use(require('./rutas/Objeto'))
app.use(require('./rutas/Bitacora'))
app.use(require('./rutas/Proyecto'))
app.use(require('./rutas/Solicitudes'))
app.use(require('./rutas/Planillas'))
app.use(require('./rutas/Empleado'))
app.use(require('./rutas/Mantenimientos'))
app.use(require('./rutas/Compras'))
app.use(require('./rutas/Gastos'))
app.use(require('./rutas/Equipos'))
app.use(require('./rutas/Cargos'))
app.use(require('./rutas/Areas'))
app.use(require('./rutas/Asignacion'))
app.use(require('./rutas/Ubicacion'))
app.use(require('./rutas/Empleados_planilla'))
app.use(require('./rutas/EstadosEquipo'))
app.use(require('./rutas/TiposEquipo'))
app.use(require('./rutas/EstadoAsignacion'))


 // Server running
app.listen(app.get('port'), () =>{
    console.log('Server running on port',app.get('port'))
})

