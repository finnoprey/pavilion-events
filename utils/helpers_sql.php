/**
 * Software Development SAT - Pavilion Events Management System (PEMS)
 *
 * Various simple functions that modify and/or execute sql queries.
 * All functions use prepared statements for security purposes.
 *
 * @author Finn Scicluna-O'Prey <finn@oprey.co>
 *
 */

<?php
  function basic_query($mysqli, $sql) {
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
  }
  
  function prepared_query($mysqli, $sql, $params, $types = "") {
    $types = $types ?: str_repeat('s', count($params));
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt;
  }

  function prepared_select($mysqli, $sql, $params = [], $types = "") {
    return prepared_query($mysqli, $sql, $params, $types)->get_result();
  }

  function prepared_select_single($mysqli, $sql, $params = [], $types = "") {
    return prepared_query($mysqli, $sql, $params, $types)->get_result()->fetch_assoc();
  }
?>