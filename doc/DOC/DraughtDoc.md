// -- Nombres de sistemas y descripción 
parrandaDraughtKPI //////////////////////////////////////////////////////////// 
# DESCRIPCION: 
Es donde se gestiona la información de visitas y clientes  
# PROCESAR ARCHIVOS:
Se procesa las visitas los clientes a los cuales se les modifica información o clientes nuevos, 
# GESTION DE CLIENTES Y PUNTOS: 
Se gestiona los clientes es decir se actualiza datos se les asigna máquinas según número de qr. Se tiene en cuenta las máquinas que están sin cliente, estas se listan y son asignables tal cual a nuevos clientes o se pueden asignar nuevas (Las nuevas se le pone un nombre de ubicación el qr y que esta activa).
# DATOS OFFLINE
Se gestionan los nomencladores y los clientes según los filtros por zona de venta vendedor municipio etc... Se descarga esta info para enviarla al vendedor por WhatsApp y este la cargue en la app.
# ELIMINAR VISITA
En este apartado se selecciona al vendedor y una fecha puntual y se eliminan las visitas procesadas ese día.
# CÓDIGO
El código está correctamente comentado en su mayoría siguiendo su ejecución en los widget según el flujo de la operación a realizar. 

SupervisorDraught-Edith 
# DESCRIPCION
Aplicación que está en proceso ya que se terminó la encuesta de prospección la cual la pondera y tiene un impacto de un 75 por ciento en la evaluación de los clientes
quedando pendiente la parte de pedidos adaptada según el distribuidor
# CODIGO
Similar a la aplicación de botella solo cambia la forma de gestionar el localStorage ya que existe una visita dentro de otra visita la cual es la visita del punto o análisis de la máquina y se hice un script de ayuda o soporte el cual actualiza variables globales que son comunes en todos los scripts de la visita con el objetivo de no reescribir tanto código y sea más cómodo y optimo la manipulación de datos.


SupervisorDraught ////////////////////////////////////////////////////////////////
# DESCRIPCION 
Aplicación actual en uso, misma descripción anterior en cuanto a código
# FUNCIONALIDADES 
LOGUIN: loguin de usuario por roles y su validación, no se valida la hora en que se loguea por situación en matanzas en el horario de trabajo con el combustible
CARGA DE DATOS: Carga de nomencladores y cliente con y sin conexión
EDITAR CLIENTE: Igual que el de botella solo con un campo de más que es si el cliente maneja formato de dispensada o va a estar dentro del proyecto de dispensada, no está la parte de tomar ubicación con el detalle del mapa. 
INICIAR VISITA: Similar a la de botella con los detalles anteriores explicados. Menos validaciones que en botella por tema de operatividad 
# PENDIENTES
Pendiente a carga de datos o métricas necesarias para que el vendedor tenga estado de cómo está el punto en cuento a compra y average de venta además de otros datos para realizar un pedido efectivo. ESTA IMPLEMENTACION SE DETUVO POR CAMBIO EN EL MODELO DE NEGOCIO DE CCSA A DISTRIBUIDORES

Dashboard ///////////////////////////////////////////////////////////////////////
# PROSPECCION
Cuantos clientes nuevos se levantaron y cuantos de esos clientes son listo para instalar, cuales son término medio con qué condiciones pendientes, y cuales remotamente no y por que
Geolocalización en mapa de estos 

# PENDIENTES 
NECESARIO 
-- Poner un nuevo rol a los vendedores de los distribuidores que los identifiquen como dispensada, 
-- Etiqueta a clientes nuevo,
Terminar rutina de visita y la calidad de esta según target y ejecutado por ccsa y distribuidor

METRICAS QUE SE EVALUAN QUE VAN A SER NECESARIAS EN LOS DASHBOARD DE LOS DISTRIBUIDORES YA QUE HOY SON EL CORE DE LAS OPERACIONES DE CCSA
COMPRAS => Ultima fecha de compra
           Cuantos días sin compra
           cantidad de compras
           análisis de compras en un lapso histórico de tiempo

DISTRIBUCION => Ultima fecha de entrega
                Días sin distribución 
                cantidad de unidades distribuidas
                análisis de distribución en un lapso histórico de tiempo
INVENTARIO => Piso de toneles por cliente
              Ultima fecha de recogida de tonel vacío / cantidad de este
              Ultima fecha de entrega de lleno / cantidad de este
              que le queda por entregar de la compra (EN EL CASO DE LOS DISTRIBUIDORES POR SU SISTEMA DE DISTRIBUICION NO ES NECESARIO HASTA AHORA DE LLEVAR ESTE DATO)
ESTADO DEL CLIENTE => A partir de las métricas anteriores el cliente se puede encontrar en un estado de bien, regular o crítico. 
                               



// -- BASE DE DATOS
 # Tablas referente a visitas
 "ccsa_visita" => Datos generales de la visita 
 "ccsa_draught_prosp_visit" => Datos especificos de la prospeccion al ser de encuestas e informativo la mayoria se guardan en formato json
 "ccsa_draught_comp" => Datos de la competencia de tonel
 "ccsa_visita_competencia" => Competencia de botella
 "ccsa_draught_point_visits" => Datos de la maquina de dispensada
 "ccsa_draught_price" => Précio según formato
 "ccsa_draught_rotation" => Rotacion en el punto
 "ccsa_rotacion_cliente" => Rotacion del producto 
 "ccsa_visita_frescor_producto" => producto dañado 
 "ccsa_orden_merchandising" => Merchandising
 "ccsa_draught_sanitizer_visit" => Visita del zanitizador 
 "ccsa_sku_price" => Précio del formato más vendido
 "ccsa_draught_co2_info" => CO2 
 "ccsa_order" => Orden
 "ccsa_order_line" => Orden detalles

"ccsa_draught_sales_point"=> Tabla de punto de ventas
"ccsa_draugth_equipment" => Tabla de maquinas





 