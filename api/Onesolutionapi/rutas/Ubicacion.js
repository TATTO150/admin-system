const express = require('express');
const routes = express.Router();

routes.get('/Ubicaciones', (req, res) => {
    req.getConnection((err, conn) => {
        if (err) return res.status(500).send(err);

        conn.query('SELECT * FROM tbl_ubicacion', (err, rows) => {
            if (err) return res.status(500).send(err);
            res.json(rows);
        });
    });
});

routes.get('/Ubicaciones/:COD_UBICACION', (req, res) => {
    const { COD_UBICACION } = req.params;
    const consulta = 'SELECT * FROM tbl_ubicacion WHERE COD_UBICACION = ?';

    req.getConnection((err, conn) => {
        if (err) return res.status(500).send(err);

        conn.query(consulta, [COD_UBICACION], (err, rows) => {
            if (err) return res.status(500).send(err);
            res.json(rows);
        });
    });
});

routes.post('/INS_UBICACION', (req, res) => {
    const {NOM_UBICACION, DESCRIPCION} = req.body;
    const consulta = 'CALL INS_UBICACION( ?, ?)';

    req.getConnection((err, conn) => {
        if (err) return res.status(500).send(err);

        conn.query(consulta, [NOM_UBICACION, DESCRIPCION], (err, rows) => {
            if (err) return res.status(500).send(err);
            res.send('Ubicación Ingresada Correctamente');
        });
    });
});

routes.put('/Ubicaciones/:COD_UBICACION', (req, res) => {
    const { COD_UBICACION } = req.params;
    const {NOM_UBICACION, DESCRIPCION } = req.body;
    const consulta = 'CALL UPD_UBICACION(?, ?, ?)';

    req.getConnection((err, conn) => {
        if (err) return res.status(500).send(err);

        conn.query(consulta, [COD_UBICACION, NOM_UBICACION, DESCRIPCION ], (err, rows) => {
            if (err) return res.status(500).send(err);
            res.send('Ubicación Actualizada Correctamente');
        });
    });
});

routes.delete('/Ubicaciones/delete/:COD_UBICACION', (req, res) => {
    const { COD_UBICACION } = req.params;
    const consulta = 'CALL ELI_UBICACION(?)';

    req.getConnection((err, conn) => {
        if (err) return res.status(500).send(err);

        conn.query(consulta, [COD_UBICACION], (err, rows) => {
            if (err) return res.status(500).send(err);
            res.send('Ubicación Eliminada Correctamente');
        });
    });
});

module.exports = routes;
