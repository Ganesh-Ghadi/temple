import React, { useEffect, useState } from "react";
import { Toaster, toast } from "sonner";

import {
  createBrowserRouter,
  createRoutesFromElements,
  Route,
  RouterProvider,
} from "react-router-dom";
import MainLayout from "./layouts/MainLayout";
import Homepage from "./pages/HomePage/Homepage";
import Projects from "./pages/Projects/Projects";
import Roles from "./pages/Roles/index";
import Users from "./pages/Users/index";
import CreateUsers from "./pages/Users/Create";
import UpdateUsers from "./pages/Users/Update";
import Devtas from "./pages/Devtas/index";
import CreateDevtas from "./pages/Devtas/Create";
import UpdateDevtas from "./pages/Devtas/Update";
import PoojaTypes from "./pages/PoojaTypes/index";
import CreatePoojaType from "./pages/PoojaTypes/Create";
import UpdatePoojaType from "./pages/PoojaTypes/Update";
import Denominations from "./pages/Denominations/index";
import CreateDenominations from "./pages/Denominations/Create";
import UpdateDenominations from "./pages/Denominations/Update";
import Login from "./pages/Login/Login";
import Register from "./pages/Register/Register";
import Error from "./customComponents/Error/Error";
import CreateProject from "./pages/Projects/CreateProject";
import ProtectedRoute from "./customComponents/ProtectedRoute/ProtectedRoute";
import GuestRoute from "./customComponents/GuestRoute/GuestRoute";
import EditProject from "./pages/Projects/EditProject";
import Tasks from "./pages/Tasks/Tasks";
import CreateTask from "./pages/Tasks/CreateTask";
import EditTask from "./pages/Tasks/EditTask";

const App = () => {
  const router = createBrowserRouter(
    createRoutesFromElements(
      <>
        <Route
          errorElement={<Error />}
          path="/"
          element={
            <ProtectedRoute>
              <MainLayout />
            </ProtectedRoute>
          }
        >
          <Route index element={<Homepage />} />
          <Route path="/projects" element={<Projects />} />
          <Route path="/roles" element={<Roles />} />
          <Route path="/users" element={<Users />} />
          <Route path="/users/create" element={<CreateUsers />} />
          <Route path="/users/:id/edit" element={<UpdateUsers />} />
          <Route path="/devtas" element={<Devtas />} />
          <Route path="/devtas/create" element={<CreateDevtas />} />
          <Route path="/devtas/:id/edit" element={<UpdateDevtas />} />
          <Route path="/pooja_types" element={<PoojaTypes />} />
          <Route path="/pooja_types/create" element={<CreatePoojaType />} />
          <Route path="/pooja_types/:id/edit" element={<UpdatePoojaType />} />
          <Route path="/denominations" element={<Denominations />} />
          <Route
            path="/denominations/create"
            element={<CreateDenominations />}
          />
          <Route
            path="/denominations/:id/edit"
            element={<UpdateDenominations />}
          />
        </Route>
        <Route
          errorElement={<Error />}
          path="/login"
          element={
            <GuestRoute>
              {" "}
              <Login />
            </GuestRoute>
          }
        />
        <Route
          errorElement={<Error />}
          path="/register"
          element={
            <GuestRoute>
              <Register />
            </GuestRoute>
          }
        />
      </>
    )
  );

  return (
    <>
      <Toaster position="top-right" />
      <RouterProvider router={router} />
    </>
  );
};

export default App;
