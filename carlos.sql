create database if not exists home_server character set utf8mb4 collate utf8mb4_unicode_ci;
use home_server;
create table eventos{
    id int auto_increment primary key,
    data date not null,
    titulo varchar(255) not null,
    tipo enum('evento', 'feriado', 'lancamento_jogo', 'lancamento_anime', 'aniversario', 'outro') default 'evento', 
    criado_em timestamp default current_timestamp,
    index idx_data (data) 
};
create table horarios_semana{
    id int auto_increment primary key,
    dia_semana tinyint not null,
    horario time not null,
    descricao varchar(255) not null,
    unique key unico_dia_horario (dia_semana, horario)
};
create table logs {
    id int auto_increment primary key,
    ip varchar(45) not null,
    pais_codigo varchar(2) default null,
    tipo enum('visita', 'clique', 'outro') not null,
    descricao varchar(255) not null,
    user_agent varchar(255) default null,
    criado_em timestamp default current_timestamp,
    index idx_criado (criado_em)
};

-- uns insert de teste... mas nao vou colocar porque eu confio em sql ja