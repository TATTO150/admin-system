const express = require('express');
const routes = express.Router();

// Obtener todos los estados de equipo
routes.get('/EstadosEquipo', (req, res) => {
    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query('SELECT * FROM tbl_estado_equipo', (err, rows) => {
            if (err) return res.send(err);
            res.send(rows);
        });
    });
});

// Insertar un nuevo estado de equipo
routes.post('/INS_ESTADO_EQUIPO', (req, res) => {
    const { DESC_ESTADO_EQUIPO } = req.body;
    const consulta = `CALL InsertarEstadoEquipo(?)`;

    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query(consulta, [DESC_ESTADO_EQUIPO], (err, rows) => {
            if (err) return res.send(err);
            res.send('Estado de Equipo Ingresado Correctamente');
        });
    });
});

// Actualizar un estado de equipo
routes.put('/EstadosEquipo/:COD_ESTADO_EQUIPO', (req, res) => {
    const { COD_ESTADO_EQUIPO } = req.params;
    const { DESC_ESTADO_EQUIPO } = req.body;
    const consulta = `CALL ActualizarEstadoEquipo(?, ?)`;

    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query(consulta, [COD_ESTADO_EQUIPO, DESC_ESTADO_EQUIPO], (err, rows) => {
            if (err) return res.send(err);
            res.send('Estado de Equipo Actualizado Correctamente');
        });
    });
});

// Eliminar un estado de equipo
routes.delete('/EstadosEquipo/:COD_ESTADO_EQUIPO', (req, res) => {
    const { COD_ESTADO_EQUIPO } = req.params;
    const consulta = `CALL EliminarEstadoEquipo(?)`;

    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query(consulta, [COD_ESTADO_EQUIPO], (err, rows) => {
            if (err) return res.send(err);
            res.send('Estado de Equipo Eliminado Correctamente');
        });
    });
});

module.exports = routes;