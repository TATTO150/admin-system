const express = require ('express')
const routes = express.Router()

routes.get('/Contrasena',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT * FROM tbl_ms_hist_contraseña' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Contrasena/:Id_Hist',(req,res)=>{
    const {Id_Hist}=req.params;
    const consulta='SELECT * FROM tbl_ms_hist_contraseña where Id_Hist=?';
    req.getConnection((err,conn)=>{
        conn.query(consulta,[Id_Hist],(err,rows)=>{
            if(err)return res.send(err)
                res.send(rows)
        })
    })
})
routes.post('/INS_HIST_CONTRASENA',(req, res)=>{
    const {Id_Hist, Id_Usuario,Contrasena} = req.body;
    const consulta = `call INS_HIST_CONTRASENA('${Id_Hist}','${Id_Usuario}','${Contrasena}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Contraseña Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


    //ACTUALIZAR CONTRASEÑA
    routes.put('/Contrasena/:Id_Hist',(req, res)=>{
        const { Id_Hist} = req.params;
        const { Id_Usuario,Contrasena} = req.body;
        const consulta = `call UPD_HIST_CONTRASENA(?,'${Id_Usuario}','${Contrasena}')`;
        req.getConnection((err, conn)=>{
                conn.query(consulta, [Id_Hist], (err, rows)=>{
                    if(!err)
                    res.send('Contraseña Actualizado Correctamente')
                    else
                    console.log(err)
                })
            })
        })

   //ELIMINAR CONTRASEÑA
        routes.delete('/Contrasena/delete/:Id_Hist',(req, res)=>{
            const { Id_Hist} = req.params;
            const consulta = `call ELI_HIST_CONTRASENA(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [Id_Hist], (err, rows)=>{
                        if(!err)
                        res.send('Contraseña Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
