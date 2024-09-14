const express = require ('express')
const routes = express.Router()

routes.get('/bitacoras',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_bitacora' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/bitacoras/:ID_bitacora',(req,res)=>{
    const {ID_bitacora}=req.params;
    const consulta='SELECT * FROM tbl_bitacora where ID_bitacora=?';
    req.getConnection((err,conn)=>{
        conn.query(consulta,[ID_bitacora],(err,rows)=>{
            if(err)return res.send(err)
                res.send(rows)
        })
    })
})

//AGREGAR
routes.post('/INS_BITACORA',(req, res)=>{
    const {ID_bitacora, Id_usuario, Id_Hist, Id_Objetos , Id_Parametro,Id_Rol, event_bitacora ,	frm_gestion_bitacora, Fecha_ingreso} = req.body;
    const consulta = `call INS_BITACORA('${	ID_bitacora}','${Id_usuario}''${Id_Hist}','${Id_Objetos}','${Id_Parametro}','${Id_Rol}','${event_bitacora}','${frm_gestion_bitacora}''${Fecha_ingreso}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Datos ingresados correctamente')
                else
                console.log(err)
            })
        })
    })
    

    //ELIMINAR BITACORA
    routes.delete('/bitacoras/delete/:ID_bitacora',(req, res)=>{
        const { ID_bitacora} = req.params;
        const consulta = `call ELI_BITACORA(?)`;
        req.getConnection((err, conn)=>{
                conn.query(consulta, [ID_bitacora], (err, rows)=>{
                    if(!err)
                    res.send('Datos eliminados correctamente')
                    else
                    console.log(err)
                })
            })
        }) 

module.exports = routes

