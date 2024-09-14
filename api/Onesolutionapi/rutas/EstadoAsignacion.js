const express = require('express');
const routes = express.Router();

// Obtener todos los estados de asignación
routes.get('/EstadoAsignacion', (req, res) => {
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query('SELECT * FROM tbl_estado_asignacion', (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send(rows);
        });
    });
});

// Obtener un estado de asignación por su código
routes.get('/EstadoAsignacion/:COD_ESTADO_ASIGNACION', (req, res) => {
    const { COD_ESTADO_ASIGNACION } = req.params;
    const consulta = 'SELECT * FROM tbl_estado_asignacion WHERE COD_ESTADO_ASIGNACION = ?';

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query(consulta, [COD_ESTADO_ASIGNACION], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }

            if (rows.length === 0) {
                console.log('No se encontraron estados de asignación con COD_ESTADO_ASIGNACION:', COD_ESTADO_ASIGNACION);
                return res.status(404).send({ message: 'Estado de asignación no encontrado' });
            }

            res.send(rows[0]);
        });
    });
});

// Insertar un nuevo estado de asignación
routes.post('/INS_EstadoAsignacion', (req, res) => {
    const { ESTADO } = req.body;
    const consulta = `CALL INS_EstadoAsignacion(?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query(consulta, [ESTADO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send({ message: 'Estado de asignación ingresado correctamente' });
        });
    });
});

// Actualizar un estado de asignación
routes.put('/EstadoAsignacion/:COD_ESTADO_ASIGNACION', (req, res) => {
    const { COD_ESTADO_ASIGNACION } = req.params;
    const { ESTADO } = req.body;
    const consulta = `CALL UDP_EstadoAsignacion(?, ?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        const parametros = [COD_ESTADO_ASIGNACION, ESTADO];

        conn.query(consulta, parametros, (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }

            res.send({ message: 'Estado de asignación actualizado correctamente' });
        });
    });
});

// Eliminar un estado de asignación
routes.delete('/EstadoAsignacion/:COD_ESTADO_ASIGNACION', (req, res) => {
    const { COD_ESTADO_ASIGNACION } = req.params;
    const consulta = `CALL DEL_EstadoAsignacion(?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query(consulta, [COD_ESTADO_ASIGNACION], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send({ message: 'Estado de asignación eliminado correctamente' });
        });
    });
});

module.exports = routes;
