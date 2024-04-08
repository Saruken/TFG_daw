create database db;

create table usuario(
    nick varchar(50) not null,
    contraseña varchar(20) not null,
    correo varchar(50) not null,
    f_nac date,
    imagen varchar(100),
    tipo varchar(50) not null,
    estado tinyint(1) not null,
    constraint pk_usuario primary key (nick)
);

create table juego(
    id bigint(20) not null,
    nombre varchar(100) not null,
    f_lanzamiento date not null,
    precio float(5,2) not null,
    imagen varchar(100),
    activo tinyint(1) not null,
    usuario varchar(50) not null,
    constraint pk_juego primary key (id),
    constraint fk_jue_usu foreign key (usuario) references usuario(nick)
);

create table juegos_usuario(
    usuario varchar(50) not null,
    juego bigint(20) not null,
    estado tinyint(1) not null,
    constraint pk_coleccion primary key (usuario,juego),
    constraint fk_col_usu foreign key (usuario) references usuario(nick),
    constraint fk_col_jue foreign key (juego) references juego(id)
);

create table opinion(
    usuario varchar(50) not null,
    juego bigint(20) not null,
    texto varchar(200),
    puntos int(5) not null,
    constraint pk_opinion primary key (usuario,juego),
    constraint fk_opi_usu foreign key (usuario) references usuario(nick),
    constraint fk_opi_jue foreign key (juego) references juego(id)
);

create table categoria(
    id bigint(20) not null auto_increment,
    nombre varchar(100) not null,
    constraint pk_categoria primary key (id)
);

create table categoria_juego(
    juego bigint(20) not null,
    categoria bigint(20) not null,
    constraint pk_cat_jue primary key (juego,categoria),
    constraint fk_cj_jue foreign key (juego) references juego(id),
    constraint fk_cj_cat foreign key (categoria) references categoria(id)
);

create table plataforma(
    id bigint(20) not null auto_increment,
    nombre varchar(100) not null,
    imagen varchar(100),
    activo tinyint(1) not null,
    constraint pk_plataforma primary key (id)
);

create table plataforma_juego(
    juego bigint(20) not null,
    plataforma bigint(20) not null,
    constraint pk_pla_jue primary key (juego,plataforma),
    constraint fk_pj_jue foreign key (juego) references juego(id),
    constraint fk_pj_pla foreign key (plataforma) references plataforma(id)
);

create table lista(
    id bigint(20) not null auto_increment,
    nombre varchar(100) not null,
    publica tinyint(1) not null,
    usuario varchar(50) not null,
    constraint pk_lista primary key (id),
    constraint fk_lis_usu foreign key (usuario) references usuario(nick)
);

create table usuarios_seguidos(
    emisor varchar(50) not null,
    receptor varchar(50) not null,
    constraint pk_amigo primary key (emisor,receptor),
    constraint fk_ami_emi foreign key (emisor) references usuario(nick),
    constraint fk_ami_rec foreign key (receptor) references usuario(nick)
);

create table juegos_lista(
    lista bigint(20) not null,
    juego bigint(20) not null,
    constraint pk_jue_lis primary key (lista,juego),
    constraint fk_jl_lis foreign key (lista) references lista(id),
    constraint fk_jl_jue foreign key (juego) references juego(id)
);

create table listas_seguidas(
    lista bigint(20) not null,
    usuario varchar(50) not null,
    constraint pk_sigue primary key (lista,usuario),
    constraint fk_lu_lis foreign key (lista) references lista(id),
    constraint fk_lu_usu foreign key (usuario) references usuario(nick)
);