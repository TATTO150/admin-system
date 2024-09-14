const express = require('express');
const routes = express.Router();

// Obtener todos los tipos de equipo
routes.get('/TiposEquipo', (req, res) => {
    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query('SELECT * FROM tbl_tipo_equipo', (err, rows) => {
            if (err) return res.send(err);
            res.send(rows);
        });
    });
});

// Insertar un nuevo tipo de equipo
routes.post('/INS_TIPO_EQUIPO', (req, res) => {
    const { TIPO_EQUIPO } = req.body;
    const consulta = `CALL InsertarTipoEquipo(?)`;

    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query(consulta, [TIPO_EQUIPO], (err, rows) => {
            if (err) return res.send(err);
            res.send('Tipo de Equipo Ingresado Correctamente');
        });
    });
});

// Actualizar un tipo de equipo
routes.put('/TiposEquipo/:COD_TIP_EQUIPO', (req, res) => {
    const { COD_TIP_EQUIPO } = req.params;
    const { TIPO_EQUIPO } = req.body;
    const consulta = `CALL ActualizarTipoEquipo(?, ?)`;

    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query(consulta, [COD_TIP_EQUIPO, TIPO_EQUIPO], (err, rows) => {
            if (err) return res.send(err);
            res.send('Tipo de Equipo Actualizado Correctamente');
        });
    });
});

// Eliminar un tipo de equipo
routes.delete('/TiposEquipo/:COD_TIP_EQUIPO', (req, res) => {
    const { COD_TIP_EQUIPO } = req.params;
    const consulta = `CALL EliminarTipoEquipo(?)`;

    req.getConnection((err, conn) => {
        if (err) return res.send(err);

        conn.query(consulta, [COD_TIP_EQUIPO], (err, rows) => {
            if (err) return res.send(err);
            res.send('Tipo de Equipo Eliminado Correctamente');
        });
    });
});

module.exports = routes;
