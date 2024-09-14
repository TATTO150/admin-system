const express = require ('express')
const routes = express.Router()

routes.get('/Proyectos',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_proyectos' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Proyectos/:COD_PROYECTO', (req, res) => {
    const { COD_PROYECTO } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_proyectos WHERE COD_PROYECTO = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_PROYECTO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron Proyecto con COD_PROYECTO:', COD_PROYECTO);
                return res.status(404).send({ message: 'Proyecto no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_PROYECTOS',(req, res)=>{
    const {NOM_PROYECTO,FEC_INICIO,FEC_FINAL,DESC_PROYECTO,PRESUPUESTO_INICIO,ESTADO_PROYECTO} = req.body;
    const consulta = `call INS_PROYECTOS('${NOM_PROYECTO}','${FEC_INICIO}','${FEC_FINAL}','${DESC_PROYECTO}','${PRESUPUESTO_INICIO}','${ESTADO_PROYECTO}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Proyecto Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })
    //ACTUALIZAR PROYECTO
    routes.put('/Proyectos/:COD_PROYECTO', (req, res) => {
        const { COD_PROYECTO } = req.params;
        const {NOM_PROYECTO,FEC_INICIO,FEC_FINAL,DESC_PROYECTO,PRESUPUESTO_INICIO ,ESTADO_PROYECTO} = req.body;
    
        const consulta = `
            CALL UPD_PROYECTOS(
                ?, ?, ?, ?, ?,?,?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_PROYECTO,
                NOM_PROYECTO,
                FEC_INICIO,
                FEC_FINAL,
                DESC_PROYECTO,
                PRESUPUESTO_INICIO,
                ESTADO_PROYECTO,
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Proyecto Actualizado Correctamente');
            });
        });
    });
    
  // ELIMINAR PROYECTO
routes.delete('/Proyectos/delete/:COD_PROYECTO', (req, res) => {
    const { COD_PROYECTO } = req.params;
    const consulta = `CALL ELI_PROYECTOS(?)`;
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión:', err);
            return res.status(500).send('Error en la conexión a la base de datos');
        }
        conn.query(consulta, [COD_PROYECTO], (err, rows) => {
            if (err) {
                console.error('Error al ejecutar la consulta:', err);
                return res.status(500).send('Error al eliminar el proyecto');
            }
            res.send('Proyecto Eliminado Correctamente');
        });
    });
});


module.exports = routes
