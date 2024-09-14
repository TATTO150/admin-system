const express = require('express');
const routes = express.Router();

// Obtener todas las asignaciones de equipo
routes.get('/Asignaciones', (req, res) => {
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query('SELECT * FROM tbl_equipo_asignacion', (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send(rows);
        });
    });
});

// Obtener una asignación por su código
routes.get('/Asignaciones/:COD_ASIGNACION_EQUIPO', (req, res) => {
    const { COD_ASIGNACION_EQUIPO } = req.params;
    const consulta = 'SELECT * FROM tbl_equipo_asignacion WHERE COD_ASIGNACION_EQUIPO = ?';

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query(consulta, [COD_ASIGNACION_EQUIPO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }

            if (rows.length === 0) {
                console.log('No se encontraron asignaciones con COD_ASIGNACION_EQUIPO:', COD_ASIGNACION_EQUIPO);
                return res.status(404).send({ message: 'Asignación no encontrada' });
            }

            res.send(rows[0]);
        });
    });
});

// Insertar una nueva asignación
routes.post('/INS_ASIGNACION', (req, res) => {
    const { COD_EQUIPO, COD_EMPLEADO, COD_PROYECTO, DESCRIPCION, COD_ESTADO_ASIGNACION, FECHA_ASIGNACION_INICIO } = req.body;
    const consulta = `CALL INS_EquipoAsignacion(?, ?, ?, ?, ?, ?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        const parametros = [COD_EQUIPO, COD_EMPLEADO, COD_PROYECTO, DESCRIPCION, COD_ESTADO_ASIGNACION, FECHA_ASIGNACION_INICIO];

        conn.query(consulta, parametros, (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send({ message: 'Asignación ingresada correctamente' });
        });
    });
});

// Actualizar una asignación
routes.put('/Asignaciones/:COD_ASIGNACION_EQUIPO', (req, res) => {
    const { COD_ASIGNACION_EQUIPO } = req.params;
    const { COD_EQUIPO, COD_EMPLEADO, COD_PROYECTO, DESCRIPCION, COD_ESTADO_ASIGNACION, FECHA_ASIGNACION_INICIO, FECHA_ASIGNACION_FIN } = req.body;

    const consulta = `CALL UDP_EquipoAsignacion(?, ?, ?, ?, ?, ?, ?, ?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        const parametros = [COD_ASIGNACION_EQUIPO, COD_EQUIPO, COD_EMPLEADO, COD_PROYECTO, DESCRIPCION, COD_ESTADO_ASIGNACION, FECHA_ASIGNACION_INICIO, FECHA_ASIGNACION_FIN];

        conn.query(consulta, parametros, (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }

            res.send({ message: 'Asignación actualizada correctamente' });
        });
    });
});

// Eliminar una asignación
routes.delete('/Asignaciones/:COD_ASIGNACION_EQUIPO', (req, res) => {
    const { COD_ASIGNACION_EQUIPO } = req.params;
    const consulta = `CALL ELI_EquipoAsignacion(?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query(consulta, [COD_ASIGNACION_EQUIPO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send({ message: 'Asignación eliminada correctamente' });
        });
    });
});

module.exports = routes;