const express = require('express');
const routes = express.Router();

// Obtener todos los equipos
routes.get('/Equipos', (req, res) => {
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query('SELECT * FROM tbl_equipo', (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send(rows);
        });
    });
});

// Obtener un equipo por su código
routes.get('/Equipos/:COD_EQUIPO', (req, res) => {
    const { COD_EQUIPO } = req.params;
    const consulta = 'SELECT * FROM tbl_equipo WHERE COD_EQUIPO = ?';

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query(consulta, [COD_EQUIPO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }

            if (rows.length === 0) {
                console.log('No se encontraron equipos con COD_EQUIPO:', COD_EQUIPO);
                return res.status(404).send({ message: 'Equipo no encontrado' });
            }

            res.send(rows[0]);
        });
    });
});

// Insertar un nuevo equipo
routes.post('/INS_EQUIPO', (req, res) => {
    const { NOM_EQUIPO, COD_TIP_EQUIPO, DESC_EQUIPO, COD_ESTADO_EQUIPO, FECHA_COMPRA, VALOR_EQUIPO } = req.body;
    const consulta = `CALL InsertarEquipo(?, ?, ?, ?, ?, ?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        const parametros = [NOM_EQUIPO, COD_TIP_EQUIPO, DESC_EQUIPO, COD_ESTADO_EQUIPO, FECHA_COMPRA, VALOR_EQUIPO];

        conn.query(consulta, parametros, (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send({ message: 'Equipo ingresado correctamente' });
        });
    });
});

// Actualizar un equipo
routes.put('/Equipos/:COD_EQUIPO', (req, res) => {
    const { COD_EQUIPO } = req.params;
    const { NOM_EQUIPO, COD_TIP_EQUIPO, DESC_EQUIPO, COD_ESTADO_EQUIPO, FECHA_COMPRA, VALOR_EQUIPO } = req.body;

    const consulta = `CALL ActualizarEquipo(?, ?, ?, ?, ?, ?, ?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        const parametros = [COD_EQUIPO, NOM_EQUIPO, COD_TIP_EQUIPO, DESC_EQUIPO, COD_ESTADO_EQUIPO, FECHA_COMPRA, VALOR_EQUIPO];

        conn.query(consulta, parametros, (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }

            res.send({ message: 'Equipo actualizado correctamente' });
        });
    });
});

// Eliminar un equipo
routes.delete('/Equipos/delete/:COD_EQUIPO', (req, res) => {
    const { COD_EQUIPO } = req.params;
    const consulta = `CALL EliminarEquipo(?)`;

    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send({ message: 'Error en la conexión a la base de datos', error: err });
        }

        conn.query(consulta, [COD_EQUIPO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send({ message: 'Error en la consulta SQL', error: err });
            }
            res.send({ message: 'Equipo eliminado correctamente' });
        });
    });
});

module.exports = routes;
